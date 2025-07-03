// Multistep Form Plugin
(function() {
    'use strict';

    var multistepsForm = {
        init: function() {
            this.form = document.querySelector('.multisteps-form__form');
            this.progressBtns = document.querySelectorAll('.multisteps-form__progress-btn');
            this.panels = document.querySelectorAll('.multisteps-form__panel');
            this.nextBtns = document.querySelectorAll('.js-btn-next');
            this.prevBtns = document.querySelectorAll('.js-btn-prev');
            
            this.bindEvents();
        },

        bindEvents: function() {
            // Progress buttons
            this.progressBtns.forEach(function(btn, index) {
                btn.addEventListener('click', function() {
                    multistepsForm.showPanel(index + 1);
                });
            });

            // Next buttons
            this.nextBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    multistepsForm.nextStep();
                });
            });

            // Previous buttons
            this.prevBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    multistepsForm.prevStep();
                });
            });
        },

        showPanel: function(step) {
            // Update progress buttons
            this.progressBtns.forEach(function(btn, index) {
                if (index < step) {
                    btn.classList.add('js-active');
                } else {
                    btn.classList.remove('js-active');
                }
            });

            // Show/hide panels
            this.panels.forEach(function(panel, index) {
                if (index === step - 1) {
                    panel.classList.add('js-active');
                } else {
                    panel.classList.remove('js-active');
                }
            });
        },

        nextStep: function() {
            var currentPanel = document.querySelector('.multisteps-form__panel.js-active');
            var currentStep = Array.from(this.panels).indexOf(currentPanel) + 1;
            
            if (this.validateStep(currentStep)) {
                if (currentStep < this.panels.length) {
                    this.showPanel(currentStep + 1);
                }
            }
        },

        prevStep: function() {
            var currentPanel = document.querySelector('.multisteps-form__panel.js-active');
            var currentStep = Array.from(this.panels).indexOf(currentPanel) + 1;
            
            if (currentStep > 1) {
                this.showPanel(currentStep - 1);
            }
        },

        validateStep: function(step) {
            var currentPanel = document.querySelector('.multisteps-form__panel.js-active');
            var requiredFields = currentPanel.querySelectorAll('[required]');
            var isValid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            return isValid;
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            multistepsForm.init();
        });
  } else {
        multistepsForm.init();
    }

    // Export for global use
    window.multistepsForm = multistepsForm;

})();