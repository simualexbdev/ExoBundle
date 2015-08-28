<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 06/07/15
 * Time: 12:11
 */

namespace UJM\ExoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class isValidTimedQcmMark extends Constraint
{
    public $message = 'is_valid_timedQcm_mark';

    public function validatedBy()
    {
        return 'ujm.exercise_isvalidtimedQcmmark';
    }
}