<?php
/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 06/07/15
 * Time: 12:13
 */

namespace UJM\ExoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\HttpFoundation\Request;

class isValidTimedQcmMarkValidator extends ConstraintValidator
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request->request->all();
    }
    public function validate($value, Constraint $constraint)
    {
        $interTimedQcm = $this->request['ujm_exobundle_interactiontimedQcmtype'];

        if (isset($interTimedQcm['weightResponse'])) {
            if (!preg_match('/^-?\d+(?:[.,]\d+)?$/', $value, $matches)) {
                $this->context->addViolation($constraint->message, array('%string%' => $value));
            }
        }
    }
}