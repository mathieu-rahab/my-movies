
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const searchType = document.getElementById('searchType').value;
        const searchQuery = document.getElementById('searchQuery').value;

        let endpoint = '';
        switch(searchType) {
            case 'actor':
                endpoint = '../ajax/searchActor.php';
                break;
            case 'director':
                endpoint = '../ajax/searchDirector.php';
                break;
            case 'movie':
                endpoint = '../ajax/searchMovie.php';
                break;
        }

        const url = `${endpoint}?searchQuery=${encodeURIComponent(searchQuery)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => displayResults(data))
            .catch(error => console.error('Error:', error));
    });

// Sert à afficher les résultats de la recherche simple    
    function displayResults(data) {
        console.log(data);
        const resultsContainer = document.getElementById('results');
        resultsContainer.innerHTML = '';

        if (data.error) {
            resultsContainer.innerHTML = `<p>Error: ${data.error}</p>`;
        } else if (Array.isArray(data)) {  // Vérifie si data est un tableau
            data.forEach(item => {
                const itemDiv = document.createElement('div');
                Object.keys(item).forEach(key => {
                    const content = document.createElement('p');
                    content.textContent = `${key}: ${item[key]}`;
                    itemDiv.appendChild(content);
                });
                resultsContainer.appendChild(itemdiv);
            });
        } else if (data) {  // Ajout pour traiter data comme un objet simple si ce n'est pas un tableau
            const itemDiv = document.createElement('div');
            Object.keys(data).forEach(key => {
                const content = document.createElement('p');
                content.textContent = `${key}: ${data[key]}`;
                itemDiv.appendChild(content);
            });
            resultsContainer.appendChild(itemDiv);
        } else {
            resultsContainer.innerHTML = '<p>No results found or data is not an array.</p>';
        }
    }

});
