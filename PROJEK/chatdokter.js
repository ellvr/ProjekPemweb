function showChat(doctorName, doctorImage = '1.png') {
    // Sembunyikan pesan default
    document.getElementById('default-message').style.display = 'none';
    // Tampilkan header chat
    document.getElementById('chat-header').style.display = 'flex';
    // Tampilkan area chat
    document.getElementById('chat-content').style.display = 'block';
    
    // Set nama dokter di header chat
    document.getElementById('doctor-chat-name').textContent = doctorName;
    
    // Set gambar dokter di header chat
    document.getElementById('doctor-chat-image').src = doctorImage;

    // Menampilkan pesan contoh dari dokter
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.innerHTML = `<p class="received-message">Hi, saya ${doctorName}. Apa yang bisa saya bantu?</p>`;
}

function sendMessage() {
    const input = document.getElementById('message-input');
    const message = input.value;
    if (message.trim()) {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML += `<p class="sent-message">${message}</p>`;
        input.value = ''; 
        chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll ke bawah otomatis

        // Tampilkan balasan dokter
        setTimeout(() => {
            chatMessages.innerHTML += `<p class="received-message">Terima kasih! Pesan Anda: "${message}" telah diterima.</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll ke bawah otomatis
        }, 1000); // Balasan muncul setelah 1 detik
    }
}
