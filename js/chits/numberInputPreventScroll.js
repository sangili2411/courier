function preventNumberInputChanges(event) {
    // Prevent arrow key changes
    if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
        event.preventDefault();
    }
}

function preventMouseWheel(event) {
    // Prevent mouse wheel changes
    event.preventDefault();
}

// Get all number inputs
const numberInputs = document.querySelectorAll('input[type="number"]');

// Attach event listeners to each number input
numberInputs.forEach(input => {
    input.addEventListener('keydown', preventNumberInputChanges);
    input.addEventListener('wheel', preventMouseWheel, {
        passive: false
    });
});