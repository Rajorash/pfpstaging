<?php

namespace App\Http\Livewire\Business;

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Business;
use App\Models\License;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class CreateBusinessForm extends Component
{

    /**
     * The state of the modal. If modal is showing, then
     * $isOpen = true
     *
     * @var boolean
     */
    public $isOpen = false;

    /**
     * Boolean flag to mark the form failed due to
     * limitations or missing requirements
     */
    public $failure = false;

    /**
     * A message to be displayed explaining the failure
     * to the user.
     */
    public string $failureMessage = '';

    /**
     * The name of the business to be created
     *
     * @var string
     */
    public $businessname;

    /**
     * The email of the user to attach the business to
     * after creation. Must exist in the system already
     * for validation.
     *
     * @var [type]
     */
    public $email;

    /**
     * Validation rules for form fields.
     *
     * @var array
     */
    protected $rules = [
        'businessname' => 'required|string|min:4|unique:businesses,name',
        'email' => [
            'required',
            'email',
            'exists:users,email'
        ],
    ];

    /**
     * Custom validation messages
     *
     * You can override the validation response messages
     * here.
     *
     * @var array
     */
    protected $messages = [
        'email.exists' => "That email is not registered."
    ];

    /**
     * @var App\Http\Controllers\UserController
     */
    private $UserController;

    /**
     * CreateEditUser constructor.
     * @param  null  $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->UserController = new UserController();
    }

    /**
     * Function fires on initial load and check
     * prerequisites for creating a business
     *
     * @return void
     */
    public function mount()
    {
        $user = Auth::user();

        // validate that the user is an advisor and so can create businesses
        if (!$user->isAdvisor()) {
            $this->failure = true;
            $this->failureMessage = 'Only advisors can create new businesses and assign them to users.';

            return;
        }

        // check that the advisor has sufficent free licenses to create a new business
        // if ($user->licenses->available() < 1) {
        //     $this->failure = true;
        //     $this->failureMessage = 'You have insufficient free licenses available to create a new business.';

        //     return;
        // }
    }

    /**
     * Magic function fires when a property updates on
     * the form. Used mostly for instant validation
     * feedback.
     *
     * @param [type] $propertyName
     * @return void
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Sets the modal to open state
     *
     * @return void
     */
    public function openBusinessForm()
    {

        $this->isOpen = true;

        $this->dispatchBrowserEvent('creating-business');
    }


    /**
     * Validates the fields submitted and upon success will attempt
     * to create and assign the business.
     *
     * @return void
     */
    public function submitForm()
    {
        // validate the form fields, using rules set under $rules
        $data = $this->validate();

        $this->dispatchBrowserEvent('processing-business');

        $owner = User::where('email', $data['email'])->first();

        $ownersRoles = $owner->roles->pluck('id', 'id')->toArray();

        if (!$this->UserController->checkClient($ownersRoles)) {
            $this->failure = true;
            $this->failureMessage = 'Only clients can be added to Business.';
        } else {

            $advisor = Auth::user();

            // create the business and assign to the user
            $new_business = new Business;
            $new_business->name = $data['businessname'];
            $new_business->owner()->associate($owner);
            $new_business->save();

            // assign the license to the advisor and the business
            $license = License::create([
                'active' => true,
                'account_number' => uniqid(),
                'business_id' => $new_business->id,
                'advisor_id' => $advisor->id,
            ]);

            $new_business->license()->save($license);

            $this->isOpen = false;

            $key = 'Business_all';
            Cache::forget($key);

            return redirect("/business");
        }
    }

    public function render()
    {
        return view('business.create-business-form');
    }
}
