<?php

/**
 * Created by CPA SIMUSANTE.
 * User: user
 * Date: 29/06/15
 * Time: 16:05
 */

namespace UJM\ExoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * TypeTimedQcm
 *
 * @ORM\Table(name="ujm_type_timedQcm")
 * @ORM\Entity(repositoryClass="UJM\ExoBundle\Repository\TypeTimedQcmRepository")
 */
class TypeTimedQcm
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(unique=true, name="code", type="integer")
     */
    private $code;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return TypeTimedQcm
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set code
     *
     * @param integer $code
     *
     * @return TypeTimedQcm
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    public function __toString()
    {
        return $this->value;
    }
}

