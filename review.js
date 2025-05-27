// DOM elements
const reviewForm = document.getElementById("reviewForm");
const reviewerName = document.getElementById("reviewerName");
const reviewText = document.getElementById("reviewText");
const reviewsContainer = document.getElementById("reviewsContainer");

// Load reviews from localStorage
function loadReviews() {
    const reviews = JSON.parse(localStorage.getItem("productReviews")) || [];
    reviewsContainer.innerHTML = ""; // Clear existing
    reviews.forEach(review => {
        displayReview(review.name, review.text);
    });
}

// Save a new review
function saveReview(name, text) {
    const reviews = JSON.parse(localStorage.getItem("productReviews")) || [];
    reviews.unshift({ name, text }); // Add to top
    localStorage.setItem("productReviews", JSON.stringify(reviews));
}

// Display review on the page
function displayReview(name, text) {
    const review = document.createElement("div");
    review.className = "reviewItem";
    review.innerHTML = `
        <strong>${name}</strong>
        <p>${text}</p>
    `;
    reviewsContainer.appendChild(review);
}

// Handle form submit
reviewForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = reviewerName.value.trim();
    const text = reviewText.value.trim();
    if (name && text) {
        saveReview(name, text);
        displayReview(name, text);
        reviewerName.value = "";
        reviewText.value = "";
    }
});

// Load reviews on page load
document.addEventListener("DOMContentLoaded", loadReviews);
