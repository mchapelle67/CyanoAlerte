<?php

namespace App\Validator;

use App\Event\HoneyPotEvent;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\IsNullValidator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class HoneyPotValidator extends IsNullValidator
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof HoneyPot) {
            throw new UnexpectedTypeException($constraint, HoneyPot::class);
        }

        if (null !== $value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(HoneyPot::HONEYPOT)
                ->addViolation();

            $this->eventDispatcher->dispatch(new HoneyPotEvent());
        }
    }
}