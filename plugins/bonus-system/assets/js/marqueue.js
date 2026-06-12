(function () {
    const bonusesParent = document.querySelector('.marqueue ul');
    const bonuses = bonusesParent.children;
    const maxWidth = getWidth();
    bonusesParent.style.width = maxWidth;
    let interval = null;
    for (const bonus of bonuses) {
        bonus.style.width = maxWidth;
    }
    const prevButton = document.querySelector('.marqueue-prev');
    const nextButton = document.querySelector('.marqueue-next');
    prevButton.addEventListener('click', prev);
    nextButton.addEventListener('click', next);
    let currentIndex = 0;
    activate(currentIndex);

    function next() {
        deactivate(currentIndex);
        const newIndex = (currentIndex + 1) % bonuses.length;
        activate(newIndex);
    }

    function prev() {
        deactivate(currentIndex);
        let newIndex = (currentIndex - 1) % bonuses.length;
        while (newIndex < 0) {
            newIndex += (newIndex + bonuses.length) % bonuses.length;
        }
        activate(newIndex);
    }

    function activate(index) {
        const bonus = bonuses[index];        
        if (bonus) {
            bonus.classList.add('_active');
            currentIndex = index;
        }
        interval = setInterval(next, 1000);
    }

    function deactivate() {
        clearInterval(interval);
        if (bonuses[currentIndex]) {
            bonuses[currentIndex].classList.remove('_active');
        }
    }

    function getWidth() {
        let maxWidth = 0;
        for (const bonus of bonuses) {
            const width = bonus.getBoundingClientRect().width;
            if (width > maxWidth) {
                maxWidth = width;
            }
        }
        return `${maxWidth}px`;
    }
})();