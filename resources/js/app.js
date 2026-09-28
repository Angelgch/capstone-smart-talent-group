// document.addEventListener('DOMContentLoaded', () => {
//     const loginForm = document.getElementById('loginForm');
//     if (!loginForm) return;

//     loginForm.addEventListener('submit', async (e) => {
//         e.preventDefault();
//         const email = document.getElementById('email').value;
//         const password = document.getElementById('password').value;

//         try {
//             const res = await fetch('api/index.php?action=login', {
//                 method: 'POST',
//                 headers: { 'Content-Type': 'application/json' },
//                 body: JSON.stringify({ email, password })
//             });
//             const data = await res.json();

//             if (data.status === 'success') {
//                 window.location.href = data.role === 'Rol_Administrador' ? 'admin.html' : 'cliente.html';
//             } else {
//                 const err = document.getElementById('errorMsg');
//                 err.innerText = data.message;
//                 err.style.display = 'block';
//             }
//         } catch (error) {
//             console.error('Error al iniciar sesión:', error);
//         }
//     });
// });