<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProcedureAdjustedFlowsTotalByDatePeriod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "DROP PROCEDURE IF	EXISTS `AdjustedFlowsTotalByDate`;

            CREATE PROCEDURE `AdjustedFlowsTotalByDate` ( IN account_id INT, IN date CHAR ( 10 ) )
            BEGIN
                SELECT
                    a.allocation_date AS allocation_date,
                    sum((
                            a.amount * f.certainty / 100 *
                        IF
                        ( f.negative_flow = 1, - 1, 1 ))) AS suma
                FROM
                    allocations AS a
                    INNER JOIN account_flows AS f ON a.allocatable_id = f.id
                WHERE
                    a.allocatable_type LIKE '%Flow'
                    AND f.account_id = account_id
                    AND date( a.allocation_date ) = date
                GROUP BY
                    a.allocation_date
                ORDER BY
                    a.allocation_date;

            END;";

        \DB::unprepared($procedure);


        $procedure = "DROP PROCEDURE IF EXISTS `AdjustedFlowsTotalByDatePeriod`;

            CREATE PROCEDURE `AdjustedFlowsTotalByDatePeriod` (
                IN account_id INT,
                IN date_start CHAR ( 10 ),
                IN date_end CHAR ( 10 ))

            BEGIN
                SELECT
                    a.allocation_date AS allocation_date,
                    sum((
                            a.amount * f.certainty / 100 *
                        IF
                        ( f.negative_flow = 1, - 1, 1 ))) AS suma
                FROM
                    allocations AS a
                    INNER JOIN account_flows AS f ON a.allocatable_id = f.id
                WHERE
                    a.allocatable_type LIKE '%Flow'
                    AND f.account_id = account_id
                    AND date( a.allocation_date ) >= date_start
                    AND date( a.allocation_date ) <= date_end
                GROUP BY
                    a.allocation_date
                ORDER BY
                    a.allocation_date;

            END;";

        \DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedureRemove = "DROP PROCEDURE IF EXISTS `AdjustedFlowsTotalByDate`;";
        \DB::unprepared($procedureRemove);

        $procedureRemove = "DROP PROCEDURE IF EXISTS `AdjustedFlowsTotalByDatePeriod`;";
        \DB::unprepared($procedureRemove);
    }
}
