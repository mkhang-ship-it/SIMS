// Bank Settings Management JavaScript
// Handles bank selection, local JSON file integration, and form validation
// Features: Auto-load banks from local JSON file, auto-fill bank information, form validation
// Updated: Support both settings form and my-profile form

class BankSettingsManager {
    constructor() {
        this.bankSelect = document.getElementById('bank-select');
        this.bankForm = document.getElementById('bank-form');
        this.banksData = [];
        this.apiUrl = '/club/settings/banks';

        this.init();
    }

    init() {
        if (!this.bankSelect) {
            return;
        }

        this.setupEventListeners();
        this.loadBanksIfNeeded();
    }

    setupEventListeners() {
        // Load banks when payment tab is shown (for settings)
        const paymentTabBtn = document.getElementById('tab-payment-btn');
        if (paymentTabBtn) {
            paymentTabBtn.addEventListener('click', () => {
                if (this.banksData.length === 0) {
                    this.loadBanksFromAPI();
                }
            });
        }

        // Load banks when bank modal is shown (for my-profile)
        const bankModal = document.getElementById('editBankAccountModal');
        if (bankModal) {
            bankModal.addEventListener('show.bs.modal', () => {
                if (this.banksData.length === 0) {
                    this.loadBanksFromAPI();
                }
            });
        }

        // Handle bank selection change
        this.bankSelect.addEventListener('change', (e) => {
            this.handleBankSelection(e);
        });

        // Form validation (for settings form)
        if (this.bankForm) {
            this.bankForm.addEventListener('submit', (e) => {
                if (!this.validateForm()) {
                    e.preventDefault();
                }
            });
        }

        // Form validation (for my-profile modal form)
        const myProfileBankForm = document.querySelector('#editBankAccountModal form');
        if (myProfileBankForm) {
            myProfileBankForm.addEventListener('submit', (e) => {
                if (!this.validateMyProfileForm()) {
                    e.preventDefault();
                }
            });
        }
    }

    loadBanksIfNeeded() {
        // Load banks immediately if payment tab is active (for settings)
        const paymentTab = document.getElementById('tab-payment');
        if (paymentTab && paymentTab.classList.contains('active')) {
            this.loadBanksFromAPI();
        }
    }

    async loadBanksFromAPI() {
        this.showLoadingState();

        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();

            if (data.success && data.banks) {
                this.banksData = data.banks;
                this.populateBankSelect();
            } else {
                this.showErrorState('Không thể tải danh sách ngân hàng từ API');
            }
        } catch (error) {
            console.error('Error loading banks:', error);
            this.showErrorState('Lỗi kết nối khi tải danh sách ngân hàng');
        }
    }

    showLoadingState() {
        if (this.bankSelect) {
            this.bankSelect.innerHTML = '<option value="">Đang tải danh sách ngân hàng...</option>';
        }
    }

    showErrorState(message) {
        if (this.bankSelect) {
            this.bankSelect.innerHTML = `<option value="">${message}</option>`;
        }
    }

    populateBankSelect() {
        if (!this.bankSelect) {
            return;
        }

        this.bankSelect.innerHTML = '<option value="">-- Chọn ngân hàng --</option>';

        // Sort banks by name (Vietnamese locale)
        this.banksData.sort((a, b) => a.name.localeCompare(b.name, 'vi'));

        this.banksData.forEach(bank => {
            const option = document.createElement('option');
            option.value = bank.id;
            option.textContent = `${bank.name} (${bank.shortName})`;
            option.dataset.bank = JSON.stringify(bank);
            this.bankSelect.appendChild(option);
        });
    }

    handleBankSelection(event) {
        const selectedOption = event.target.options[event.target.selectedIndex];

        if (selectedOption.value && selectedOption.dataset.bank) {
            const bank = JSON.parse(selectedOption.dataset.bank);
            this.autoFillBankInfo(bank);
        } else {
            this.clearBankInfo();
        }
    }

    autoFillBankInfo(bank) {
        const fields = {
            'bank_name': bank.name,
            'bank_code': bank.code,
            'bank_bin': bank.bin,
            'bank_short_name': bank.shortName,
            'bank_swift_code': bank.swift_code
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = value;
            }
        });
    }

    clearBankInfo() {
        const fields = ['bank_name', 'bank_code', 'bank_bin', 'bank_short_name', 'bank_swift_code'];

        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = '';
            }
        });
    }

    validateForm() {
        const bankSelect = document.getElementById('bank-select');
        const accountName = document.getElementById('bank_account_name');
        const accountNumber = document.getElementById('bank_account_number');

        let isValid = true;
        let errorMessage = '';

        // Check bank selection
        if (!bankSelect.value) {
            errorMessage = 'Vui lòng chọn ngân hàng';
            bankSelect.focus();
            isValid = false;
        }
        // Check account name
        else if (!accountName.value.trim()) {
            errorMessage = 'Vui lòng nhập tên chủ tài khoản';
            accountName.focus();
            isValid = false;
        }
        // Check account number
        else if (!accountNumber.value.trim()) {
            errorMessage = 'Vui lòng nhập số tài khoản';
            accountNumber.focus();
            isValid = false;
        }

        if (!isValid) {
            this.showValidationError(errorMessage);
        }

        return isValid;
    }

    validateMyProfileForm() {
        const bankSelect = document.getElementById('bank-select');
        const accountName = document.getElementById('bank_account_name');
        const accountNumber = document.getElementById('bank_account_number');

        let isValid = true;
        let errorMessage = '';

        // Check bank selection
        if (!bankSelect.value) {
            errorMessage = 'Vui lòng chọn ngân hàng';
            bankSelect.focus();
            isValid = false;
        }
        // Check account name
        else if (!accountName.value.trim()) {
            errorMessage = 'Vui lòng nhập tên chủ tài khoản';
            accountName.focus();
            isValid = false;
        }
        // Check account number
        else if (!accountNumber.value.trim()) {
            errorMessage = 'Vui lòng nhập số tài khoản';
            accountNumber.focus();
            isValid = false;
        }

        if (!isValid) {
            this.showMyProfileValidationError(errorMessage);
        }

        return isValid;
    }

    showValidationError(message) {
        // Create or update error alert for settings form
        let errorAlert = document.getElementById('bank-validation-error');

        if (!errorAlert) {
            errorAlert = document.createElement('div');
            errorAlert.id = 'bank-validation-error';
            errorAlert.className = 'alert alert-danger mt-3';
            errorAlert.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle fs-18 me-2"></i>
                    <span>${message}</span>
                </div>
            `;

            // Insert before the form
            if (this.bankForm) {
                this.bankForm.parentNode.insertBefore(errorAlert, this.bankForm);
            }
        } else {
            errorAlert.querySelector('span').textContent = message;
        }

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorAlert && errorAlert.parentNode) {
                errorAlert.remove();
            }
        }, 5000);
    }

    showMyProfileValidationError(message) {
        // Create or update error alert for my-profile modal
        let errorAlert = document.getElementById('my-profile-bank-validation-error');

        if (!errorAlert) {
            errorAlert = document.createElement('div');
            errorAlert.id = 'my-profile-bank-validation-error';
            errorAlert.className = 'alert alert-danger mt-3';
            errorAlert.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle fs-18 me-2"></i>
                    <span>${message}</span>
                </div>
            `;

            // Insert in the modal body
            const modalBody = document.querySelector('#editBankAccountModal .modal-body');
            if (modalBody) {
                modalBody.appendChild(errorAlert);
            }
        } else {
            errorAlert.querySelector('span').textContent = message;
        }

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorAlert && errorAlert.parentNode) {
                errorAlert.remove();
            }
        }, 5000);
    }
}

// Initialize bank settings manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    new BankSettingsManager();
});
