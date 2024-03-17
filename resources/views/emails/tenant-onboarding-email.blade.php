<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Lyst</title>
</head>
<body>
    <h1>Welcome to Lyst, {{ $tenant->first_name }}!</h1> 
    <p>Thank you for joining our platform. We are excited to have you on board!</p>

    <p>As a tenant on Lyst, you can now: </p>
    <ul>
        <li>Browse through the list of properties available for rent.</li>
        <li>View the details of each property and make a booking request.</li>
    </ul>

    <p><strong>To get started, please complete your onboarding using this link:</strong></p>
    <p><a href="{{ $onboardingLink }}">Onboarding Link</a></p> 

    <p>You can find your new home at {{ $property->name }}. Let us know if you have any questions</p>

    <p>If you have any further questions, please don't hesitate to reach out to our support team.</p>

    <p>Best regards,</p>
    <p>The Lyst Team</p>
</body>
</html>
