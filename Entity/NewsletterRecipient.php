<?php
/**
 * @name        NewsletterRecipient
 * @package		BiberLtd\Core\Bundles\NewsletterBundle
 *
 * @author      Can Berkol
 * @author		Murat Ünal
 * @version     1.0.1
 * @date        24.01.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Core\Bundles\NewsletterBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="newsletter_recipient",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_newsletter_recipient_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_newsletter_recipient_date_modified", columns={"date_modified"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_newsletter_recipient_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_newsletter_recipient_email", columns={"email"})
 *     }
 * )
 */
class NewsletterRecipient extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     */
    private $email;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $status;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_modified;

    /** 
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $key_activation;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="recipient", referencedColumnName="id", onDelete="CASCADE")
     */
    private $member;

    /** 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Core\Bundles\NewsletterBundle\Entity\Newsletter",
     *     inversedBy="receipients"
     * )
     * @ORM\JoinColumn(name="newsletter", referencedColumnName="id", nullable=false)
     */
    private $newsletter;
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/
    /**
     * @name            getId()
     *  				Gets $id property.
     * .
     * @author          Murat Ünal
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name                  setDateModified ()
     *                                        Sets the date_modified property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_modified
     *
     * @return          object                $this
     */
    public function setDateModified($date_modified) {
        if(!$this->setModified('date_modified', $date_modified)->isModified()) {
            return $this;
        }
		$this->date_modified = $date_modified;
		return $this;
    }

    /**
     * @name            getDateModified ()
     *                                  Returns the value of date_modified property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_modified
     */
    public function getDateModified() {
        return $this->date_modified;
    }

    /**
     * @name                  setEmail ()
     *                                 Sets the email property.
     *                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $email
     *
     * @return          object                $this
     */
    public function setEmail($email) {
        if(!$this->setModified('email', $email)->isModified()) {
            return $this;
        }
		$this->email = $email;
		return $this;
    }

    /**
     * @name            getEmail ()
     *                           Returns the value of email property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @name                  setKeyActivation ()
     *                                         Sets the key_activation property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $key_activation
     *
     * @return          object                $this
     */
    public function setKeyActivation($key_activation) {
        if(!$this->setModified('key_activation', $key_activation)->isModified()) {
            return $this;
        }
		$this->key_activation = $key_activation;
		return $this;
    }

    /**
     * @name            getKeyActivation ()
     *                                   Returns the value of key_activation property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->key_activation
     */
    public function getKeyActivation() {
        return $this->key_activation;
    }

    /**
     * @name                  setMember ()
     *                                  Sets the member property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $member
     *
     * @return          object                $this
     */
    public function setMember($member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

    /**
     * @name            getMember ()
     *                            Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @name            setNewsletterCategory ()
     *                  Sets the newsletter_category property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $newsletter
     *
     * @return          object                $this
     */
    public function setNewsletterCategory($newsletter) {
        if(!$this->setModified('newsletter', $newsletter)->isModified()) {
            return $this;
        }
		$this->newsletter = $newsletter;
		return $this;
    }

    /**
     * @name            getNewsletterCategory ()
     *                  Returns the value of newsletter_category property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->newsletter_category
     */
    public function getNewslletter() {
        return $this->newsletter;
    }

    /**
     * @name                  setStatus ()
     *                                  Sets the status property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $status
     *
     * @return          object                $this
     */
    public function setStatus($status) {
        if(!$this->setModified('status', $status)->isModified()) {
            return $this;
        }
		$this->status = $status;
		return $this;
    }

    /**
     * @name            getStatus ()
     *                            Returns the value of status property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->status
     */
    public function getStatus() {
        return $this->status;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Murat Ünal
 * 23.01.2014
 * **************************************
 * A getNewsletter()
 * A setNewsletter()
 * D getNewsletterCategory()
 * D setNewsletterCategory()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 10.09.2013
 * **************************************
 * A getDateAdded()
 * A getDateModified()
 * A getEmail()
 * A getId()
 * A getKeyActivation()
 * A getMember()
 * A getNewsletterCategory()
 * A getStatus()
 *
 * A setDateAdded()
 * A setDateModified()
 * A setEmail()
 * A setKeyActivation()
 * A set_member()
 * A setNewsletterCategory()
 * A setStatus()
 *
 */