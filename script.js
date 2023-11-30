// document.addEventListener('DOMContentLoaded', function() {
//     fetch('index.php')
//         .then(response => response.json())
//         .then(data => {
//             const datalist = document.getElementById('citiesDatalist');
//             data.forEach(city => {
//                 const option = document.createElement('option');
//                 option.value = city;
//                 datalist.appendChild(option);
//             });
//         })
//         .catch(error => console.error('Ошибка при получении городов:', error));
// });

function showAlert(message) {
    alert(message);
}
