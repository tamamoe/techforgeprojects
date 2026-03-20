//javascript.js
// save and apply theme
const savedTheme = localStorage.getItem("theme");

if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
} else {
    document.body.classList.add("light-mode");
}

// toggle theme button
const toggleBtn = document.querySelector(".theme-toggle-btn");

if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
        if (document.body.classList.contains("dark-mode")) {
            document.body.classList.remove("dark-mode");
            document.body.classList.add("light-mode");
            localStorage.setItem("theme", "light");
        } else {
            document.body.classList.remove("light-mode");
            document.body.classList.add("dark-mode");
            localStorage.setItem("theme", "dark");
        }
    });
}


document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('.carousel');
    const prevBtn = document.querySelector('.nav-btn.prev');
    const nextBtn = document.querySelector('.nav-btn.next');

    if (carousel && prevBtn && nextBtn) {
        const cards = carousel.querySelectorAll('.card');
        let currentIndex = 0;

        function scrollToCard(index) {
            const cardWidth = carousel.offsetWidth;
            carousel.scrollTo({ left: cardWidth * index, behavior: 'smooth' });
            currentIndex = index;
        }

        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            let newIndex = currentIndex - 1;
            if (newIndex < 0) newIndex = cards.length - 1; // loop to last
            scrollToCard(newIndex);
        });

        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            let newIndex = currentIndex + 1;
            if (newIndex >= cards.length) newIndex = 0; // loop to first
            scrollToCard(newIndex);
        });
    
    	let autoScroll = setInterval(() => {
    	let newIndex = currentIndex + 1;
    	if (newIndex >= cards.length) newIndex = 0; // loop back
   	 	scrollToCard(newIndex);
   	 	}, 3500);
    }
});


// ADD TO CART
document.querySelectorAll(".add-cart").forEach(button => {
    button.addEventListener("click", () => {
        
        const box = button.closest(".product-box");

        const item = {
            name: box.querySelector("h3").innerText,
            price: box.querySelector(".price").innerText
        };

        let basket = JSON.parse(localStorage.getItem("basket")) || [];
        basket.push(item);

        localStorage.setItem("basket", JSON.stringify(basket));

        window.location.href = "basket.html";
    });
});


// DISPLAY ITEMS ON BASKET PAGE
if (document.getElementById("basketItems")) {

    let basket = JSON.parse(localStorage.getItem("basket")) || [];
    let container = document.getElementById("basketItems");

    basket.forEach(item => {
        container.innerHTML += `
            <div class="basket-item">
                <div class="item-details">
                    <h3>${item.name}</h3>
                    <p>${item.price}</p>
                </div>
            </div>
        `;
    });
}

// PULLS INFO FOR SEARCH BAR
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('live-search-input');
    const searchDropdown = document.getElementById('search-results-dropdown');

    // ensures the bar exists on the page
    if (searchInput && searchDropdown) {
        
        // check for typing
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // requires 2 characters to search
            if (query.length >= 2) {
                // fetch from search.php
                fetch('search.php?q=' + encodeURIComponent(query))
                    .then(response => response.text())
                    .then(data => {
                        searchDropdown.innerHTML = data;
                        searchDropdown.style.display = 'block'; // shows box
                    })
                    .catch(error => console.error('Error fetching search results:', error));
            } else {
                searchDropdown.style.display = 'none'; // hides box if less than 2 letters
                searchDropdown.innerHTML = '';
            }
        });

        // closes dropdown if user clicks elsewhere
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchDropdown.contains(event.target)) {
                searchDropdown.style.display = 'none';
            }
        });
        
        // shows dropdown again if they click search box again
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && searchDropdown.innerHTML.trim() !== '') {
                searchDropdown.style.display = 'block';
            }
        });
    }
});
