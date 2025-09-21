// 🚀 Wait for the full DOM to load before running any Igny8 logic
document.addEventListener("DOMContentLoaded", function () {
  // 🎯 Step 1: Check for auto mode first
  const autoContent = document.getElementById("igny8-auto-content");
  if (autoContent) {
    // Auto mode: Generate content immediately
    generateAutoContent(autoContent);
    return;
  }

  // 🎯 Step 2: Get the Igny8 trigger button
  const trigger = document.getElementById("igny8-launch");
  if (!trigger) return; // Exit if trigger is not present on the page

  // 🎬 Step 2: Handle button click to start personalization
  trigger.addEventListener("click", function () {
    // 🧱 Step 3: Show initial loading message
    const output = document.getElementById("igny8-output");
    output.innerHTML = "<div>⏳ Loading personalization form...</div>";

    // 📋 Step 4: Get the list of field IDs to render (e.g., "4,5,6")
    const formFields = trigger.dataset.formFields || '';

    // 🏗️ Step 5: Build query string parameters for AJAX request
    const params = new URLSearchParams();
    params.append('action', 'igny8_get_fields');
    params.append('post_id', trigger.dataset.postId);
    params.append('form_fields', formFields);

    // ➕ Step 5b: Append all other context values from data-* attributes
    Object.entries(trigger.dataset).forEach(([key, val]) => {
      if (!['ajaxUrl', 'postId', 'formFields'].includes(key)) {
        params.append(key, val);
      }
    });

    // 🌐 Step 6: Make AJAX call to load dynamic form HTML
    fetch(`${trigger.dataset.ajaxUrl}?${params.toString()}`)
      .then(res => res.text())
      .then(html => {
        // 🎨 Step 7: Inject form into the output div
        output.innerHTML = html;

const contextEl = document.getElementById("igny8-context");
const pageContentField = document.querySelector("#igny8-form [name='PageContent']");

if (contextEl && pageContentField) {
  // ✅ Clone context DOM to safely manipulate without touching original
  const clone = contextEl.cloneNode(true);

  // ✅ Replace all <br> tags with actual newlines
  clone.querySelectorAll('br').forEach(br => br.replaceWith('\n'));

  const cleanedText = clone.textContent
    .replace(/\n{2,}/g, '\n')     // Collapse multiple newlines
    .replace(/[ \t]+\n/g, '\n')   // Remove trailing space/tab before newlines
    .trim();

  pageContentField.value = `[SOURCE:JS-injected PageContext]\n\n` + cleanedText;
}





        // 🧼 Step 7b: Hide original teaser + button
        trigger.closest("#igny8-trigger").style.display = "none";

        // 📝 Step 8: Bind form submission logic (only once)
        const form = document.getElementById("igny8-form");
        if (form && !form.dataset.bound) {
          form.dataset.bound = "true";

          form.addEventListener("submit", function (e) {
            e.preventDefault();

            // 🔍 Step 8.1: Fill PageContent with context from admin-defined shortcode
            const contextEl = document.getElementById('igny8-context');
            const pageContentField = form.querySelector('[name="PageContent"]');
            if (contextEl && pageContentField && !pageContentField.value.trim()) {
              pageContentField.value = contextEl.textContent.trim();
            }

            // 🧾 Step 9: Gather form inputs for GPT
            const formData = new FormData(form);
            const resultBox = document.getElementById("igny8-generated-content");

            // ⏳ Step 9b: Show loading spinner while GPT responds
            resultBox.innerHTML = `
              <div class='igny8-loading'>
                <div class='igny8-spinner'></div>
                <span>Personalizing...</span>
              </div>
            `;


            // 🤖 Step 10: Send form data to backend for OpenAI processing
            fetch(trigger.dataset.ajaxUrl + "?action=igny8_generate_custom&post_id=" + trigger.dataset.postId, {
              method: "POST",
              body: formData,
            })
              .then(res => res.text())
              .then(html => {
                resultBox.innerHTML = html; // ✅ Show GPT output
              })
              .catch(err => {
                // ❌ Step 11: Handle backend/GPT errors
                resultBox.innerHTML = "<div style='color:red;'>⚠️ Failed to personalize content.</div>";
                console.error("Igny8 generation error:", err);
              });
          });
        }
      })
      .catch(err => {
        // ❌ Step 12: Handle form loading errors (e.g. network/API issues)
        output.innerHTML = "<div style='color:red;'>⚠️ Failed to load Igny8 form.</div>";
        console.error("Igny8 form load error:", err);
      });
  });
});

// Auto mode function: Generate content immediately without user interaction
function generateAutoContent(autoContent) {
  const loadingDiv = autoContent.querySelector('.igny8-loading');
  const resultDiv = autoContent.querySelector('#igny8-generated-content');
  
  // Show loading message
  loadingDiv.style.display = 'block';
  resultDiv.style.display = 'none';
  
  // Get form fields and context
  const formFields = autoContent.dataset.formFields || '';
  const postId = autoContent.dataset.postId;
  const ajaxUrl = autoContent.dataset.ajaxUrl;
  
  // Build query string parameters
  const params = new URLSearchParams();
  params.append('action', 'igny8_get_fields');
  params.append('post_id', postId);
  params.append('form_fields', formFields);
  
  // Append all other context values from data-* attributes
  Object.entries(autoContent.dataset).forEach(([key, val]) => {
    if (!['ajaxUrl', 'postId', 'formFields'].includes(key)) {
      params.append(key, val);
    }
  });
  
  // First, get the form fields
  fetch(`${ajaxUrl}?${params.toString()}`)
    .then(res => res.text())
    .then(html => {
      // Create a temporary container to parse the form
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      
      // Extract form data
      const form = tempDiv.querySelector('form');
      if (!form) {
        throw new Error('No form found');
      }
      
      const formData = new FormData();
      const inputs = form.querySelectorAll('input, select, textarea');
      
      inputs.forEach(input => {
        if (input.name && input.value) {
          formData.append(input.name, input.value);
        }
      });
      
      // Inject context if available
      const contextEl = document.getElementById("igny8-context");
      const pageContentField = form.querySelector("[name='PageContent']");
      
      if (contextEl && pageContentField) {
        const clone = contextEl.cloneNode(true);
        clone.querySelectorAll('br').forEach(br => br.replaceWith('\n'));
        const cleanedText = clone.textContent
          .replace(/\n{2,}/g, '\n')
          .replace(/[ \t]+\n/g, '\n')
          .trim();
        formData.set('PageContent', cleanedText);
      }
      
      // Now generate the content
      return fetch(ajaxUrl + "?action=igny8_generate_custom&post_id=" + postId, {
        method: "POST",
        body: formData,
      });
    })
    .then(res => res.text())
    .then(html => {
      // Hide loading, show result
      loadingDiv.style.display = 'none';
      resultDiv.innerHTML = html;
      resultDiv.style.display = 'block';
    })
    .catch(err => {
      // Handle errors
      loadingDiv.innerHTML = '<div style="color:red;">⚠️ Failed to generate personalized content.</div>';
      console.error("Igny8 auto generation error:", err);
    });
}

// Manual save function for when auto-save is disabled
function igny8_save_content_manual(button) {
  const form = document.querySelector('#igny8-form');
  if (!form) {
    alert('Form not found - cannot save field inputs');
    return;
  }
  
  // Get the generated content
  const contentContainer = document.querySelector('#igny8-generated-content .igny8-final-content');
  if (!contentContainer) {
    alert('No generated content found to save');
    return;
  }
  
  const content = contentContainer.innerHTML;
  const postId = document.querySelector('[data-post-id]')?.dataset.postId || 
                 document.querySelector('#igny8-launch')?.dataset.postId;
  
  if (!postId) {
    alert('Post ID not found');
    return;
  }
  
  // Get field inputs from the form - enumerate all form elements
  // This should match exactly what $_POST contains in auto-save
  const fieldInputs = {};
  const formElements = form.querySelectorAll('input, select, textarea');
  
  for (const element of formElements) {
    if (element.name && element.name !== 'submit' && element.name !== 'PageContent') {
      // Include all fields, even empty ones - use actual value or empty string
      // This matches the behavior of $_POST in PHP
      fieldInputs[element.name] = element.value || '';
    }
  }
  
  // Also check for any hidden fields that might not be in the querySelector
  const hiddenElements = form.querySelectorAll('input[type="hidden"]');
  for (const element of hiddenElements) {
    if (element.name && element.name !== 'submit' && element.name !== 'PageContent') {
      fieldInputs[element.name] = element.value || '';
    }
  }
  
  // Debug logging
  console.log('Igny8 Manual Save Debug:');
  console.log('Form elements found:', formElements.length);
  console.log('Hidden elements found:', hiddenElements.length);
  console.log('Field inputs collected:', fieldInputs);
  console.log('Field inputs JSON:', JSON.stringify(fieldInputs));
  
  if (Object.keys(fieldInputs).length === 0) {
    alert('No field inputs found - cannot save');
    return;
  }
  
  // Show loading state
  button.disabled = true;
  button.textContent = '💾 Saving...';
  
  // Prepare AJAX data
  const ajaxData = new FormData();
  ajaxData.append('action', 'igny8_save_content_manual');
  ajaxData.append('content', content);
  ajaxData.append('post_id', postId);
  ajaxData.append('field_inputs', JSON.stringify(fieldInputs));
  
  // Debug logging
  console.log('Igny8 Save Debug:');
  console.log('Content length:', content.length);
  console.log('Post ID:', postId);
  console.log('Field inputs:', fieldInputs);
  console.log('JSON string:', JSON.stringify(fieldInputs));
  
  // Send AJAX request
  fetch('/wp-admin/admin-ajax.php', {
    method: 'POST',
    body: ajaxData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      button.textContent = '✅ Saved!';
      button.style.backgroundColor = '#28a745';
      
      // Show success message
      const message = document.createElement('div');
      message.style.cssText = 'background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;';
      message.innerHTML = `✅ ${data.data.message}`;
      form.parentNode.insertBefore(message, form.nextSibling);
      
      // Reset button after 3 seconds
      setTimeout(() => {
        button.disabled = false;
        button.textContent = '💾 Save Content';
        button.style.backgroundColor = '#28a745';
        message.remove();
      }, 3000);
    } else {
      throw new Error(data.data.message || 'Save failed');
    }
  })
  .catch(error => {
    button.disabled = false;
    button.textContent = '💾 Save Content';
    console.error('Igny8 Save Error:', error);
    alert('Save failed: ' + error.message);
  });
}

// Make save function globally accessible for onclick handlers
window.igny8_save_content_manual = igny8_save_content_manual;
