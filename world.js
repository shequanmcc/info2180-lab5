window.addEventListener("DOMContentLoaded", function () {
    const countryBtn = document.getElementById("lookup");
    const citiesBtn  = document.getElementById("lookup-cities");
    const input      = document.getElementById("country");
    const resultDiv  = document.getElementById("result");

    function fetchResults(mode) {
        const country = input.value.trim();

        const params = new URLSearchParams();
        if (country !== "") {
            params.append("country", country);
        }
        params.append("lookup", mode); // "country" or "cities"

        // Optional: show loading message
        resultDiv.innerHTML = "<p>Loading...</p>";

        fetch("world.php?" + params.toString())
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                console.error(error);
                resultDiv.innerHTML = "<p>There was an error loading results.</p>";
            });
    }

    if (countryBtn) {
        countryBtn.addEventListener("click", function (e) {
            e.preventDefault();
            fetchResults("country");
        });
    }

    if (citiesBtn) {
        citiesBtn.addEventListener("click", function (e) {
            e.preventDefault();
            fetchResults("cities");
        });
    }
});
