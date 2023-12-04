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

// function resetFilters() {
//     document.getElementById('filter_marka').value = '';
//     document.getElementById('filter_city').value = '';
//     document.getElementById('filter_color').value = '';
    
//     var checkboxes = document.querySelectorAll('input[type="checkbox"]');
//     checkboxes.forEach(function(checkbox) {
//         checkbox.checked = false;
//     });

//     document.querySelector('.filter').submit();
// }

function showAlert(message) {
    alert(message);
}
