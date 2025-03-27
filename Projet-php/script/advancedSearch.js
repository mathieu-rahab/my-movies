document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');

    searchForm.addEventListener('submit', function(event) {
        event.preventDefault();

        // Collecte des données du formulaire
        const type = document.getElementById('type').value;
        const title = document.getElementById('title').value;
        const year = document.getElementById('year').value;
        const yearOperator = document.getElementById('yearOperator').value;
        const yearLogic = document.getElementById('yearLogic').value;
        const tag = document.getElementById('tag').value;
        const tagLogic = document.getElementById('tagLogic').value;
        const name = document.getElementById('name').value;
        const id = document.getElementById('id').value;

        // Permet de construire dyanmiquement l'url
        let url = `../ajax/advancedSearch.php?type=${type}`;
        if (title) url += `&title=${encodeURIComponent(title)}`;
        if (year && yearOperator && yearLogic) url += `&year=${year}&year_operator=${yearOperator}&year_logic=${yearLogic}`;
        if (tag && tagLogic) url += `&tag=${encodeURIComponent(tag)}&tag_logic=${tagikLogic}`;
        if (name) url += `&name=${encodeURIComponent(name)}`;
        if (id) url += `&id=${id}`;

        // Envoi de la requête
        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log(data);  // Process and display the data
                displayResults(data);
            })
            .catch(error => console.error('Error:', error));
    });
});

//Sert à afficher les résultats de la recherche avancée
function displayResults(data) {
    const resultsContainer = document.getElementById('results');
    resultsContainer.innerHTML = '';

    if (data.error) {
        resultsContainer.innerHTML = `<p>${data.error}</p>`;
    } else {
        data.forEach(item => {
            const itemElement = document.createElement('div');
            for (const key in item) {
                const content = document.createElement('p');
                content.textContent = `${key}: ${item[key]}`;
                itemElement.appendChild(content);
            }
            resultsContainer.appendChild(itemElement);
        });
    }
}
