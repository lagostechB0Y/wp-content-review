console.log('WPCR admin review JS loaded');

// Handle Approve and Reject button clicks
document.addEventListener('click', function(e) {
    // Check if clicked element is an approve or reject button
    if (e.target.id !== 'wpcr-approve-btn' && e.target.id !== 'wpcr-reject-btn') {
        return;
    }

    e.preventDefault();
    
    // Get the action from data attribute (data-wpcr-action)
    const action = e.target.getAttribute('data-wpcr-action');
    const postId = document.getElementById('wpcr_post_id');
    const nonce = document.querySelector('input[name="_wpcr_nonce_wpcr_review_action"]');
    const note = document.getElementById('wpcr_review_note');

    if (!postId || !nonce) {
        alert('Error: Missing required form data');
        return;
    }

    if (typeof WPCR === 'undefined' || !WPCR.adminPostUrl) {
        alert('Error: Admin URL not available');
        return;
    }

    // Show confirmation
    if (!confirm('Are you sure? Post will be ' + (action === 'approve' ? 'PUBLISHED' : 'REVERTED TO DRAFT'))) {
        return;
    }

    // Create a hidden form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = WPCR.adminPostUrl;
    form.style.display = 'none';

    // Add form fields
    const actionField = document.createElement('input');
    actionField.type = 'hidden';
    actionField.name = 'action';
    actionField.value = 'wpcr_review_action';
    form.appendChild(actionField);

    const postIdField = document.createElement('input');
    postIdField.type = 'hidden';
    postIdField.name = 'post_id';
    postIdField.value = postId.value;
    form.appendChild(postIdField);

    const reviewActionField = document.createElement('input');
    reviewActionField.type = 'hidden';
    reviewActionField.name = 'review_action';
    reviewActionField.value = action;
    form.appendChild(reviewActionField);

    const noteField = document.createElement('input');
    noteField.type = 'hidden';
    noteField.name = 'review_note';
    noteField.value = note ? note.value : '';
    form.appendChild(noteField);

    const nonceField = document.createElement('input');
    nonceField.type = 'hidden';
    nonceField.name = nonce.name;
    nonceField.value = nonce.value;
    form.appendChild(nonceField);

    // Append form to body and submit
    document.body.appendChild(form);
    form.submit();
});

// Check if buttons exist on page load
document.addEventListener('DOMContentLoaded', function() {
    const approveBtn = document.getElementById('wpcr-approve-btn');
    const rejectBtn = document.getElementById('wpcr-reject-btn');
    
    if (approveBtn) {
        console.log('WPCR: Approve button found');
    }
    if (rejectBtn) {
        console.log('WPCR: Reject button found');
    }
});