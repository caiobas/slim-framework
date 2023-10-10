<?php

declare(strict_types=1);

namespace App\Queue\Email\Template;

class EmailTemplate
{
    private string $subject;
    private EmailFromTemplate $from;
    private string $emailTo;
    private string $body;

    /**
     * @param string $subject
     * @param EmailFromTemplate $from
     * @param string $emailTo
     * @param string $body
     */
    public function __construct(string $subject, EmailFromTemplate $from, string $emailTo, string $body)
    {
        $this->subject = $subject;
        $this->from = $from;
        $this->emailTo = $emailTo;
        $this->body = $body;
    }

    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'from' => $this->from->toArray(),
            'emailTo' => $this->emailTo,
            'body' => $this->body,
        ];
    }
}
