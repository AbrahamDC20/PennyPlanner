document.addEventListener('DOMContentLoaded', () => {
    const tutorialSteps = [
        { message: 'Welcome to PennyPlanner!', element: null },
        { message: 'This is your dashboard.', element: '#dashboard' },
        { message: 'Here you can manage your transactions.', element: '#transactions' },
        { message: 'Visit settings to customize your experience.', element: '#settings' },
    ];

    let currentStep = 0;

    function showStep(step) {
        const overlay = document.createElement('div');
        overlay.className = 'tutorial-overlay';
        overlay.innerHTML = `
            <div class="tutorial-box">
                <p>${step.message}</p>
                <button id="next-step">Next</button>
            </div>
        `;
        document.body.appendChild(overlay);

        if (step.element) {
            const target = document.querySelector(step.element);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.classList.add('highlight');
            }
        }

        document.getElementById('next-step').addEventListener('click', () => {
            document.body.removeChild(overlay);
            if (step.element) {
                document.querySelector(step.element).classList.remove('highlight');
            }
            currentStep++;
            if (currentStep < tutorialSteps.length) {
                showStep(tutorialSteps[currentStep]);
            }
        });
    }

    document.getElementById('start-tutorial').addEventListener('click', () => {
        showStep(tutorialSteps[currentStep]);
    });
});
