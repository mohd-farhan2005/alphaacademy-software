<div id="password-change-modal" class="fixed inset-0 z-50 hidden transition-opacity" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Password Changed</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Your password has been changed by an administrator. Please enter your new password to continue your session, or log out.</p>
                                
                                <form id="verify-password-form" class="mt-4">
                                    @csrf
                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                        <input type="password" name="password" id="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        <p id="password-error" class="mt-2 text-sm text-red-600 hidden"></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 relative z-20">
                    <button type="button" id="btn-verify-password" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto relative hover:cursor-pointer z-30">Verify Password</button>
                    <button type="button" id="btn-logout-password" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto relative hover:cursor-pointer z-30">Log Out</button>
                </div>
                
                <!-- Hidden logout form to trigger actual logout -->
                <form id="hidden-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('password-change-modal');
        const verifyBtn = document.getElementById('btn-verify-password');
        const logoutBtn = document.getElementById('btn-logout-password');
        const errorMsg = document.getElementById('password-error');
        const passwordInput = document.getElementById('new_password');
        const logoutForm = document.getElementById('hidden-logout-form');

        let isChecking = false;
        let passwordChanged = false;

        // Poll every 5 seconds
        const pollInterval = setInterval(checkPasswordStatus, 5000);

        function checkPasswordStatus() {
            if (isChecking || passwordChanged) return;
            isChecking = true;

            fetch('{{ route('password.check') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if(response.status === 401 || response.status === 419) {
                    // Session expired or unauthenticated, reload to let normal auth handle it
                    window.location.reload();
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.changed) {
                    showModal();
                }
            })
            .catch(error => console.error('Error checking password status:', error))
            .finally(() => {
                isChecking = false;
            });
        }

        function showModal() {
            passwordChanged = true;
            modal.classList.remove('hidden');
            passwordInput.focus();
            clearInterval(pollInterval);
        }

        verifyBtn.addEventListener('click', function () {
            const password = passwordInput.value;
            
            if (!password) {
                errorMsg.textContent = 'Please enter your new password.';
                errorMsg.classList.remove('hidden');
                return;
            }

            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Verifying...';
            errorMsg.classList.add('hidden');

            fetch('{{ route('password.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(result => {
                if (result.status === 200 && result.body.success) {
                    // Success! Hide modal and resume polling
                    modal.classList.add('hidden');
                    passwordChanged = false;
                    passwordInput.value = '';
                    setInterval(checkPasswordStatus, 5000);
                } else {
                    // Failed
                    errorMsg.textContent = result.body.message || 'Incorrect password.';
                    errorMsg.classList.remove('hidden');
                }
            })
            .catch(error => {
                errorMsg.textContent = 'An error occurred. Please try again.';
                errorMsg.classList.remove('hidden');
            })
            .finally(() => {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify Password';
            });
        });
        
        // Allow enter key to submit
        passwordInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                verifyBtn.click();
            }
        });

        logoutBtn.addEventListener('click', function () {
            logoutForm.submit();
        });
    });
</script>
