<?php

namespace Terminalbd\CrmBundle\Entity;


use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ExpenseParticular
 *
 * @ORM\Table(name="crm_expense_particulars")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ExpenseParticularRepository")
 */
class ExpenseParticular
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var Expense
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Expense", inversedBy="expenseParticulars")
     * @ORM\JoinColumn(name="expense_id", referencedColumnName="id", nullable=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $expense;

    /**
     * @var ExpenseBatch
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\ExpenseBatch", inversedBy="expenseParticulars")
     * @ORM\JoinColumn(name="expense_batch_id", referencedColumnName="id", nullable=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $expenseBatch;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="expenseParticulars")
     * @ORM\JoinColumn(name="particular_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $particular;

    /**
     * @var integer
     * @ORM\Column(name="expense_chart_detail_id", type="integer", nullable=true)
     */
    private $expenseChartDetailId;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\File(maxSize="25M")
     */
    public $file;

    public $temp;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated", type="datetime", nullable = true)
     */

    private $updated;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        if(!file_exists( $this->getUploadDir())){
            mkdir( $this->getUploadDir(), 0777, true);
        }
        return __DIR__ . '/../../../../public/' . $this->getUploadDir();

    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/';

    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * @return Expense
     */
    public function getExpense()
    {
        return $this->expense;
    }

    /**
     * @param Expense $expense
     */
    public function setExpense($expense): void
    {
        $this->expense = $expense;
    }

    /**
     * @return ExpenseBatch
     */
    public function getExpenseBatch()
    {
        return $this->expenseBatch;
    }

    /**
     * @param ExpenseBatch $expenseBatch
     */
    public function setExpenseBatch($expenseBatch): void
    {
        $this->expenseBatch = $expenseBatch;
    }

    /**
     * @return Setting
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param Setting $particular
     */
    public function setParticular($particular): void
    {
        $this->particular = $particular;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getExpenseChartDetailId(): ?int
    {
        return $this->expenseChartDetailId;
    }

    /**
     * @param int $expenseChartDetailId
     */
    public function setExpenseChartDetailId(?int $expenseChartDetailId): void
    {
        $this->expenseChartDetailId = $expenseChartDetailId;
    }



}
