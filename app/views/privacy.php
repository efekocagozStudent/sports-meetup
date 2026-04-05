<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <h1 class="fw-bold mb-1">Privacy Policy</h1>
        <p class="text-muted small mb-4">Last updated: April 2026</p>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold">1. Who we are</h2>
                <p>SportsMeet is a student project application for organising and joining local sports events. This privacy policy explains what personal data we collect, why, and your rights under the General Data Protection Regulation (GDPR).</p>

                <h2 class="h5 fw-bold mt-4">2. Data we collect</h2>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr><th>Data</th><th>Purpose</th><th>Legal basis</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Username</td>
                            <td>Identify you to other users</td>
                            <td>Contractual necessity</td>
                        </tr>
                        <tr>
                            <td>Email address</td>
                            <td>Account login and password reset</td>
                            <td>Contractual necessity</td>
                        </tr>
                        <tr>
                            <td>Password (hashed with bcrypt)</td>
                            <td>Authentication</td>
                            <td>Contractual necessity</td>
                        </tr>
                        <tr>
                            <td>Event data you create or join</td>
                            <td>Core app functionality</td>
                            <td>Contractual necessity</td>
                        </tr>
                        <tr>
                            <td>Session cookie</td>
                            <td>Keep you signed in during a visit</td>
                            <td>Legitimate interest</td>
                        </tr>
                    </tbody>
                </table>

                <h2 class="h5 fw-bold mt-4">3. Cookies</h2>
                <p>We use one strictly-necessary session cookie (<code>PHPSESSID</code>) to maintain your login state. No tracking or advertising cookies are set. We also store your cookie consent preference in your browser's <code>localStorage</code> — this never leaves your device.</p>

                <h2 class="h5 fw-bold mt-4">4. Data retention</h2>
                <p>Your account and event data is stored for as long as your account exists. You may request deletion at any time by contacting the admin via the platform.</p>

                <h2 class="h5 fw-bold mt-4">5. Third-party services</h2>
                <p>We load Bootstrap CSS and JS from a CDN (<code>cdn.jsdelivr.net</code>). This means your browser makes a request to jsDelivr when visiting any page. jsDelivr's own privacy policy applies to that request.</p>

                <h2 class="h5 fw-bold mt-4">6. Your rights (GDPR)</h2>
                <ul>
                    <li><strong>Access</strong> — you may request a copy of your data.</li>
                    <li><strong>Rectification</strong> — you may correct inaccurate data.</li>
                    <li><strong>Erasure</strong> — you may request your account be deleted.</li>
                    <li><strong>Portability</strong> — you may request your data in a machine-readable format.</li>
                    <li><strong>Objection</strong> — you may object to processing based on legitimate interest.</li>
                </ul>
                <p>To exercise any of these rights, contact the platform administrator.</p>

                <h2 class="h5 fw-bold mt-4">7. Data security</h2>
                <p>Passwords are stored as bcrypt hashes (cost 12) and are never stored in plain text. All forms include CSRF tokens to prevent cross-site request forgery. Session cookies are set with <code>HttpOnly</code> and <code>SameSite=Lax</code> flags.</p>
            </div>
        </div>

        <p class="text-center">
            <a href="<?= url('/') ?>" class="btn btn-outline-secondary btn-sm">← Back to home</a>
        </p>
    </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
