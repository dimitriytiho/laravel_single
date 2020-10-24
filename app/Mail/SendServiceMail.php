<?php


namespace App\Mail;


use App\Models\Main;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendServiceMail extends Notification
{
    use Queueable;

    public $title;
    public $body;
    public $values;
    public $template;
    public $h1;
    private $layout;
    private $viewPath;
    private $lang;

    /**
     * Create a new notification instance.
     *
     * @return void
     *
     *
     * Переменные для отправки письма
     * $title - Заголовок письма.
     * $body - Содержимое письма, можно просто текст или вёрстку. Если используется $template дополнительный вид, то этот параметр не используется, передайте null, необязательный параметр.
     * $values - Данные для использования в видах, необязательный параметр.
     * $template - Название вида для оптравки письма из папки views/mail (к примеру user), необязательный параметр.
     * $h1 - Если нужно H1 передать из вида $template, то передайте null, тогда заголовок $title используйте в виде, который передаёте в $template, необязательный параметр.
     *
     *
     */
    public function __construct($title, $body = null, $values = null, $template = null, $h1 = true)
    {
        $this->layout = 'mail';
        $this->lang = lang();
        $this->title = $title;
        $this->body = $body;
        $this->values = $values;
        $this->template = $template;
        $this->h1 = $h1;

        $modulesPath = config('modules.path');
        $this->viewPath = config('modules.views');

        // Переопределим путь к видам
        view()->getFinder()->setPaths($modulesPath);

        if (!view()->exists("{$this->viewPath}.{$this->layout}")) {
            Main::getError("View {$this->viewPath}.{$this->layout} not found", __METHOD__, false, 'critical');
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $title = $this->title;
        $values = $this->values;
        $h1 = $this->h1;
        $body = $this->body;
        $view = null;
        $lang = $this->lang;
        $site_name = Main::site('name') ?? ' ';
        $color = config('add.scss.primary', '#ccc');

        if ($this->template && view()->exists("{$this->viewPath}.mail.{$this->template}")) {
            $view = view("{$this->viewPath}.mail.{$this->template}",
                compact('title', 'values', 'body', 'color', 'site_name'))
                ->render();
        }

        $email = Main::site('email');
        $tel = Main::site('tel');
        $tel = $tel ? __("{$lang}::s.or_call") . $tel : null;

        return (new MailMessage)
            ->view("{$this->viewPath}.{$this->layout}",
                compact('view', 'lang', 'title', 'values', 'h1', 'body', 'site_name', 'color', 'email', 'tel'))
            ->subject(__("{$this->lang}::s.Information_letter"));



        /*return (new MailMessage)
            ->greeting('Hello!')
            ->line('One of your invoices has been paid!')
            ->action('View Invoice', url('/invoice/'.$this->invoice->id))
            ->line('Thank you for using our application!');*/
    }
}
