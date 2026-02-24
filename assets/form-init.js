/**
 * Jobs Expert Plugin - Form Initialization
 * Initialisiert die moderne Formvalidierung für alle Formulare
 */

jQuery(function($) {
  'use strict';

  // Sichere Funktion zum Entfernen von HTML-Tags
  function stripHTMLTags(html) {
    if (!html || typeof html !== 'string') return '';
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
  }

  // Enhancements zur FormValidator Library für WordPress-Kompatibilität
  
  /**
   * Erweitere FormValidator um WordPress-Editor Support
   */
  if (typeof FormValidator !== 'undefined') {
    
    // Erweitere die getFieldValue Methode um WP-Editor Support
    const originalGetFieldValue = FormValidator.prototype.getFieldValue;
    FormValidator.prototype.getFieldValue = function(field) {
      // Handle WordPress Editor (TinyMCE)
      if (field.classList.contains('wp-editor-area')) {
        const editorId = field.id;
        if (window.tinymce && window.tinymce.get(editorId)) {
          let content = window.tinymce.get(editorId).getContent();
          // Entferne HTML Tags um mindestens Textlänge zu prüfen
          return stripHTMLTags(content);
        }
      }
      
      // For form-generated biography field (wp_editor)
      if (field.name === 'biography' && window.tinymce) {
        let editor = window.tinymce.get('biography');
        if (editor) {
          let content = editor.getContent();
          // Remove HTML tags for text length validation
          return stripHTMLTags(content);
        }
      }
      
      // Fallback
      return originalGetFieldValue.call(this, field);
    };

    /**
     * Improved error display with better accessibility
     */
    const originalShowFieldError = FormValidator.prototype.showFieldError;
    FormValidator.prototype.showFieldError = function(field, errors) {
      const container = this.errorContainers.get(field);
      if (!container) return;

      if (errors.length > 0) {
        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');
        container.innerHTML = errors.map(err => `<small class="text-danger d-block">${err}</small>`).join('');
        container.style.display = 'block';
        
        // Add focus outline for accessibility
        field.setAttribute('data-invalid', 'true');
      } else {
        field.classList.remove('is-invalid');
        field.setAttribute('aria-invalid', 'false');
        field.removeAttribute('data-invalid');
        container.innerHTML = '';
        container.style.display = 'none';
      }
    };
  }

  /**
   * Auto-initialize forms with data-validate attribute
   */
  $('form[data-validate]').each(function() {
    const $form = $(this);
    const validator = new FormValidator(this, {
      realTimeValidation: true
    });
    $form.data('formValidator', validator);
  });

  /**
   * Initialize Expert Form with specific rules
   */
  const $expertForm = $('.jobs-expert-form form');
  if ($expertForm.length) {
    const expertValidator = new FormValidator('.jobs-expert-form form', {
      realTimeValidation: true
    });

    // Custom validation for biography field (min 200 chars without HTML)
    const biographyField = document.querySelector('[name="biography"]');
    if (biographyField) {
      // Add to error containers if not already there
      if (!expertValidator.errorContainers.has(biographyField)) {
        const errorContainer = document.createElement('span');
        errorContainer.className = 'help-block m-b-none validation-error';
        errorContainer.setAttribute('aria-live', 'polite');
        biographyField.parentElement.appendChild(errorContainer);
        expertValidator.errorContainers.set(biographyField, errorContainer);
      }

      // Custom rules for biography
      const originalGetRules = expertValidator.getValidationRules.bind(expertValidator);
      const originalValidateField = expertValidator.validateField.bind(expertValidator);
      
      expertValidator.validateField = function(field) {
        if (field.name === 'biography') {
          return validateBiographyField(field, expertValidator);
        }
        return originalValidateField(field);
      };
    }

    // Handle draft saves (no validation required)
    $expertForm.on('click', 'button[data-status="draft"]', function(e) {
      expertValidator.clearErrors();
    });

    // Handle publish/review (with validation)
    $expertForm.on('click', 'button[data-status="publish"], button[data-status="review"]', function(e) {
      if (!expertValidator.validate()) {
        e.preventDefault();
        return false;
      }
    });
  }

  /**
   * Validate biography field (custom logic)
   */
  function validateBiographyField(field, validator) {
    const errors = [];
    let value = '';

    // Get content from TinyMCE if available
    if (window.tinymce && window.tinymce.get('biography')) {
      const editor = window.tinymce.get('biography');
      value = stripHTMLTags(editor.getContent()).trim();
    } else {
      value = field.value.trim();
    }

    // Validation rules
    if (value.length === 0) {
      errors.push('<?php echo esc_js(__("Die Biografie ist erforderlich", "psjb")); ?>');
    } else if (value.length < 200) {
      errors.push('<?php echo esc_js(__("Die Biografie muss mindestens 200 Zeichen lang sein", "psjb")); ?>');
    }

    validator.showFieldError(field, errors);
    return errors.length === 0;
  }

  /**
   * Trigger TinyMCE save before form submit
   */
  $expertForm.on('submit', function() {
    if (typeof tinyMCE !== 'undefined') {
      tinyMCE.triggerSave();
    }
  });

  /**
   * Real-time validation for common patterns
   */
  
  // Email validation
  $(document).on('blur', 'input[type="email"]', function() {
    const $field = $(this);
    const value = $field.val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (value && !emailRegex.test(value)) {
      $field.addClass('is-invalid');
      const $error = $field.next('.validation-error');
      if ($error.length) {
        $error.text('<?php echo esc_js(__("Bitte geben Sie eine g\u00fcltige E-Mail-Adresse ein", "psjb")); ?>').show();
      }
    } else {
      $field.removeClass('is-invalid');
      $field.next('.validation-error').text('').hide();
    }
  });

  // Number validation
  $(document).on('blur', 'input[type="number"]', function() {
    const value = $(this).val();
    if (value && isNaN(value)) {
      $(this).addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  // Character count display (optional)
  $(document).on('input', 'textarea[minlength]', function() {
    const $this = $(this);
    const minLength = parseInt($this.attr('minlength'));
    const currentLength = $this.val().length;
    
    if (currentLength < minLength) {
      $this.addClass('is-invalid');
    } else {
      $this.removeClass('is-invalid');
    }
  });
});
