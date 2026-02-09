document.getElementById('quizForm').addEventListener('submit', function(e) {
    const cards = document.querySelectorAll('.question-card');
    for (let card of cards) {
        if (!card.querySelector('input:checked')) {
            e.preventDefault();
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert("Please answer all questions before submitting.");
            return false;
        }
    }
});
