document.querySelector("#registration-form").addEventListener("submit", function (e) {
    // Prevent form submission initially
    e.preventDefault();

    // Get form values
    const nameWithInitials = document.getElementById("name-with-initials").value.trim();
    const participationCategory = document.getElementById("participation-category").value.trim();
    const emailAddress = document.getElementById("email-address").value.trim();
    const mobileNumber = document.getElementById("mobile-number").value.trim();
    const password = document.getElementById("password").value.trim();
   

    // Regular expressions for validation
    const passwordRegex = /^(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const mobileNumberRegex = /^[0-9]{10}$/;

    // Reset error messages
    document.querySelectorAll(".error-message").forEach((msg) => (msg.textContent = ""));
    
    let isValid = true;

    // Validation Functions
    const setError = (id, message) => {
        document.querySelector(id).textContent = message;
        isValid = false;
    };

    // Validate Name with Initials
    if (!nameWithInitials) {
        setError("#name-with-initials-error", "Name with initials is required.");
    }

    // Validate Participation Category
    if (!participationCategory) {
        setError("#participation-category-error", "Participation category is required.");
    }

    // Validate Email Address
    if (!emailRegex.test(emailAddress)) {
        setError("#email-address-error", "Please enter a valid email (e.g., example@gmail.com).");
    }

    // Validate Mobile Number
    if (!mobileNumberRegex.test(mobileNumber)) {
        setError("#mobile-number-error", "Please enter a valid 10-digit mobile number.");
    }

    // Validate Password
    if (!passwordRegex.test(password)) {
        setError("#password-error", "Password must be at least 8 characters long and contain at least one symbol.");
    }

    // Submit form if all validations pass
    if (isValid) {
        e.target.submit();
    }
});