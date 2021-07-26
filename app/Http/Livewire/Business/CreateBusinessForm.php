<?php

namespace App\Http\Livewire\Business;

use App\Events\BusinessProcessed;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Business;
use App\Models\License;
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
            'nullable',
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
     * Function fires on initial load and check
     * prerequisites for creating a business
     *
     * @return void
     */
    public function mount()
    {
        $this->validateAdvisorRole(Auth::user());
        // $this->checkLicenses(); // TO-DO - implement once licensing sorted
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

        if ($data['email']) {
            $owner = User::firstWhere('email', $data['email']);
        }

        $advisor = Auth::user();

        // create the business and assign to the user
        $new_business = new Business;
        $new_business->name = $data['businessname'];
        if ($data['email'] && $owner && $this->validateClientRole($owner)) {
            $new_business->owner()->associate($owner);
        }

        $new_business->save();

        //event after save business
        if ($data['email'] && $owner && $this->validateClientRole($owner)) {
            event(new BusinessProcessed('newOwner', $new_business, $owner));
        }

        // assign the license to the advisor and the business
        $license = License::create([
            'active' => true,
            'account_number' => uniqid(),
            'business_id' => $new_business->id,
            'advisor_id' => $advisor->id,
            'regionaladmin_id' => $advisor->regionalAdminByAdvisor->first()->id
        ]);

        $new_business->license()->save($license);

        $this->isOpen = false;

        return redirect("/business");

    }

    public function render()
    {
        return view('business.create-business-form');
    }

    /**
     * Check whether the passed user has the User::ROLE_ADVISOR role
     *
     * @param  User  $user
     * @return void
     */
    private function validateAdvisorRole(User $user)
    {
        // validate that the user is an advisor and so can create businesses
        if ($user->isAdvisor()) {
            return true;
        }

        $this->failure = true;
        $this->failureMessage = 'Only advisors can create new businesses and assign them to users.';

        return false;

    }

    /**
     * Check whether the passed user has the User::ROLE_CLIENT role
     *
     * @param  User  $user
     * @return void
     */
    private function validateClientRole(User $user)
    {
        // validate that the user is an advisor and so can create businesses
        if ($user->isClient()) {
            return true;
        }

        $this->failure = true;
        $this->failureMessage = 'Only clients can be added to Business.';

        return false;

    }

    private function checkLicenses(User $user)
    {
        // check that the advisor has sufficent free licenses to create a new business
        if ($user->licenses->available() < 1) {
            return true;
        }

        $this->failure = true;
        $this->failureMessage = 'You have insufficient free licenses available to create a new business.';

        return false;
    }
}
