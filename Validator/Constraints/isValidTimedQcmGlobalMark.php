<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 06/07/15
 * Time: 13:36
 */

namespace UJM\ExoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class isValidTimedQcmGlobalMark extends Constraint
{
    public $message = 'is_valid_timedQcm_mark';

    public function validatedBy()
    {
        return 'ujm.exercise_isvalidtimedQcmglobalmark';
    }
}