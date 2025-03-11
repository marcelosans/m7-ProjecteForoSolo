<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <style>
    /* Estilos CSS */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #1DB954;
      color: white;
      padding: 20px;
      text-align: center;
    }
    nav {
      background-color: #282828;
      color: white;
      display: flex;
      justify-content: space-around;
      padding: 10px 0;
    }
    nav a {
      color: white;
      text-decoration: none;
    }
    main {
      padding: 20px;
    }
    .profile-info {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    .profile-info img {
      border-radius: 50%;
      margin-right: 20px;
    }
    .playlists {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      grid-gap: 20px;
    }
    .playlist {
      background-color: #282828;
      border-radius: 5px;
      color: white;
      padding: 20px;
      text-align: center;
    }
  </style>
</head>
<body>
  <header>
    <h1>Spotify</h1>
  </header>
  <nav>
    <a href="#">Inicio</a>
    <a href="#">Buscar</a>
    <a href="#">Tu Biblioteca</a>
  </nav>
  <main>
    <div class="profile-info">
      <img src="https://via.placeholder.com/150" alt="Foto de perfil">
      <div>
        <h2>Nombre de Usuario</h2>
        <p>Descripción de perfil</p>
      </div>
    </div>
    <h2>Tus Playlists</h2>
    <div class="playlists">
      <div class="playlist">
        <h3>Playlist 1</h3>
        <p>Descripción de la playlist</p>
      </div>
      <div class="playlist">
        <h3>Playlist 2</h3>
        <p>Descripción de la playlist</p>
      </div>
      <div class="playlist">
        <h3>Playlist 3</h3>
        <p>Descripción de la playlist</p>
      </div>
    </div>
  </main>
</body>
</html>