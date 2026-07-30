<h2>Hello, {{ $referralUser->firstname }}!</h2>

<p>Good news!</p>

<p>
    Your invitee
    <strong>
        {{ $activatedUser->firstname }}
        {{ $activatedUser->lastname }}
    </strong>
    has been approved successfully.
</p>

<p><strong>Activation ID:</strong> {{ $activation->ActivationID }}</p>

<p><strong>Amount:</strong> ₱{{ number_format($activation->amount, 2) }}</p>

<p><strong>Status:</strong> {{ $activation->status }}</p>

<p>Thank you for inviting new members.</p>

<p>
    Regards,<br>
    {{ config('app.name') }}
</p>
