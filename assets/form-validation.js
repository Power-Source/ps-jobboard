/**
 * Modern Form Validation Library
 * Moderne Alternative zu jQuery Validation Engine
 * Nutzt HTML5 Attributes + Custom Rules
 */

class FormValidator {
  constructor(formSelector, options = {}) {
    this.form = document.querySelector(formSelector);
    this.options = {
      showErrors: true,
      realTimeValidation: true,
      submitButton: this.form ? this.form.querySelector('button[type="submit"]') : null,
      onBeforeSubmit: options.onBeforeSubmit || null,
      ...options
    };

    this.errorContainers = new Map();
    
    if (this.form) {
      this.init();
    }
  }

  /**
   * Validierungsregeln definieren
   */
  static rules = {
    required: (value) => {
      return value !== null && value !== undefined && value.toString().trim() !== '';
    },
    
    email: (value) => {
      if (!value) return true;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(value);
    },
    
    minSize: (value, size) => {
      if (!value) return true;
      return value.toString().length >= parseInt(size);
    },
    
    maxSize: (value, size) => {
      if (!value) return true;
      return value.toString().length <= parseInt(size);
    },
    
    min: (value, min) => {
      if (!value) return true;
      return parseInt(value) >= parseInt(min);
    },
    
    max: (value, max) => {
      if (!value) return true;
      return parseInt(value) <= parseInt(max);
    },
    
    number: (value) => {
      if (!value) return true;
      return !isNaN(value) && value.toString().trim() !== '';
    },
    
    url: (value) => {
      if (!value) return true;
      try {
        new URL(value);
        return true;
      } catch {
        return false;
      }
    }
  };

  /**
   * Fehlermeldungen
   */
  static messages = {
    required: 'Dieses Feld ist erforderlich',
    email: 'Bitte geben Sie eine gültige E-Mail-Adresse ein',
    minSize: 'Mindestens {0} Zeichen erforderlich',
    maxSize: 'Maximal {0} Zeichen zulässig',
    min: 'Mindestwert: {0}',
    max: 'Maximalwert: {0}',
    number: 'Bitte geben Sie eine gültige Zahl ein',
    url: 'Bitte geben Sie eine gültige URL ein'
  };

  init() {
    if (!this.form) return;
    
    this.setupFields();
    
    // Real-time Validierung
    if (this.options.realTimeValidation) {
      this.form.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('blur', () => this.validateField(field));
        field.addEventListener('change', () => this.validateField(field));
      });
    }
    
    // Form Submit
    this.form.addEventListener('submit', (e) => this.handleSubmit(e));
  }

  setupFields() {
    if (!this.form) return;
    
    this.form.querySelectorAll('[required], [data-validate]').forEach(field => {
      const errorContainer = document.createElement('span');
      errorContainer.className = 'help-block m-b-none validation-error';
      errorContainer.setAttribute('aria-live', 'polite');
      
      field.parentElement.appendChild(errorContainer);
      this.errorContainers.set(field, errorContainer);
      
      // Accessibility
      if (field.hasAttribute('required')) {
        field.setAttribute('aria-required', 'true');
      }
    });
  }

  /**
   * Validiere ein einzelnes Feld
   */
  validateField(field) {
    const rules = this.getValidationRules(field);
    const value = this.getFieldValue(field);
    const errors = [];

    for (const [ruleName, ruleParam] of Object.entries(rules)) {
      if (!FormValidator.rules[ruleName]) continue;
      
      const isValid = FormValidator.rules[ruleName](value, ruleParam);
      if (!isValid) {
        let message = FormValidator.messages[ruleName];
        if (ruleParam && message.includes('{0}')) {
          message = message.replace('{0}', ruleParam);
        }
        errors.push(message);
      }
    }

    this.showFieldError(field, errors);
    return errors.length === 0;
  }

  /**
   * Extrahiere Validierungsregeln aus Feld-Attributen
   */
  getValidationRules(field) {
    const rules = {};

    // HTML5 Attribute
    if (field.hasAttribute('required')) {
      rules.required = true;
    }
    if (field.type === 'email' || field.getAttribute('type') === 'email') {
      rules.email = true;
    }
    if (field.hasAttribute('minlength')) {
      rules.minSize = field.getAttribute('minlength');
    }
    if (field.hasAttribute('maxlength')) {
      rules.maxSize = field.getAttribute('maxlength');
    }
    if (field.type === 'number' || field.hasAttribute('pattern')) {
      rules.number = true;
    }

    // data-validate Attribute (z.B. data-validate="email|minSize:200")
    if (field.hasAttribute('data-validate')) {
      const validateAttr = field.getAttribute('data-validate');
      validateAttr.split('|').forEach(rule => {
        if (rule.includes(':')) {
          const [name, param] = rule.split(':');
          rules[name.trim()] = param.trim();
        } else {
          rules[rule.trim()] = true;
        }
      });
    }

    return rules;
  }

  /**
   * Hole Feldwert (auch aus Editoren etc.)
   */
  getFieldValue(field) {
    // Für WYSIWYG Editoren (TinyMCE)
    if (field.classList.contains('wp-editor-area')) {
      const editorId = field.id;
      if (window.tinymce && window.tinymce.get(editorId)) {
        return window.tinymce.get(editorId).getContent();
      }
    }
    
    // Standard Input
    return field.value || '';
  }

  /**
   * Zeige Fehler für ein Feld
   */
  showFieldError(field, errors) {
    const container = this.errorContainers.get(field);
    if (!container) return;

    if (errors.length > 0) {
      field.classList.add('is-invalid');
      container.innerHTML = errors.join('<br>');
      container.style.display = 'block';
    } else {
      field.classList.remove('is-invalid');
      container.innerHTML = '';
      container.style.display = 'none';
    }
  }

  /**
   * Validiere gesamte Form
   */
  validate() {
    if (!this.form) {
      console.warn('FormValidator: Form element not found for validation');
      return true;
    }
    
    let isValid = true;
    
    this.form.querySelectorAll('[required], [data-validate]').forEach(field => {
      if (!this.validateField(field)) {
        isValid = false;
      }
    });

    return isValid;
  }

  /**
   * Form Submit Handler
   */
  async handleSubmit(e) {
    if (!this.form) {
      return true;
    }
    
    if (!this.validate()) {
      e.preventDefault();
      return false;
    }

    if (this.options.onBeforeSubmit) {
      e.preventDefault();
      const result = await this.options.onBeforeSubmit();
      if (result) {
        this.form.submit();
      }
    }
  }

  /**
   * Löscht Fehler und setzt Form zurück
   */
  clearErrors() {
    if (!this.form) return;
    
    this.errorContainers.forEach((container, field) => {
      field.classList.remove('is-invalid');
      container.innerHTML = '';
      container.style.display = 'none';
    });
  }
}

/**
 * jQuery-ähnliches Interface für Kompatibilität
 */
jQuery.fn.formValidator = function(options) {
  return this.each(function() {
    const $form = jQuery(this);
    if (!$form.data('formValidator')) {
      const validator = new FormValidator(this, options);
      $form.data('formValidator', validator);
    }
  });
};

// Auto-Initialisierung für Formulare mit data-validate-form Attribut
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-validate]').forEach(form => {
    new FormValidator(form);
  });
});
