<?php
namespace MediactiveDigital\MedKit\Notifications;


use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends ResetPasswordContract
{

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {

        $token = $this->token;

        if (static::$toMailCallback) {

            return call_user_func(static::$toMailCallback, $notifiable, $token);
        }

        $fullName = $notifiable->first_name . ' ' . $notifiable->name;
        $email = $notifiable->email;

        $date = '';

        if (($passwordReset = DB::table('password_reset_tokens')->where('email', $email)->first()) && Hash::check($token, $passwordReset->token)) {

            $date = date(_i('d/m/Y à H:i'), strtotime($passwordReset->created_at));
        }

        $link = url(route('back.password.reset', [$token, 'email=' . $email], false));

        return (new MailMessage)
            ->subject(_i('Demande de réinitialisation du mot de passe'))
            ->view('medKitTheme::emails.html.reset', compact('fullName', 'date', 'link'));
    }
}
