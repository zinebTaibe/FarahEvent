
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dream Events - Beauté de la Mariée</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Allura&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital@1&family=Dancing+Script&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="style.css">

<style></style>
  
</head>
<body>
  <?php include '../../nav.html'; ?><br><br><br>
  <header class="hero">
    <div class="logo">
        
        <p>Éclat et élégance pour une beauté inoubliable.</p> 
      
      <h1>Beauté de la mariée</h1>
       
      
    </div>
   
  </header>

  <main>
    <section class="intro">
      <h2>Sublimez-vous le Jour J</h2>
      <p>
        Découvrez nos prestations dédiées à la dfouaa, à l’amaria, au maquillage et à la neggafa pour un mariage exceptionnel.
        Faites-vous choyer par nos experts !
      </p>
    </section>

    <nav class="nav-buttons">
    <button onclick="showGroup('hanaa')">Hanaa</button>
    <button onclick="showGroup('amaria')">Amaria</button>
    <button onclick="showGroup('dfouaa')">Dfouaa</button>
    <button onclick="showGroup('neggafa')">Neggafa</button>
  </nav>

  <div id="hanaa" class="image-group" style="display: block;">
    <img src="./picturces/hanna1.jpg" alt="Hanaa 1">
    <img src="./picturces/hanna2.jpeg" alt="Hanaa 2">
    <img src="./picturces/hanna3.jpg" alt="Hanaa 3">
    <img src="./picturces/hanna4.jpg" alt="Hanaa 4">
    <img src="./picturces/hanna5.jpg" alt="Hanaa 5">
  </div>

  <div id="amaria" class="image-group">
    <img src="./picturces/amaria1.jpg" alt="Amaria 1">
    <img src="./picturces/amaria2.jpg" alt="Amaria 2">
    <img src="./picturces/amaria3.jpg" alt="Amaria 3">
  
    <img src="./picturces/amaria6.jpg" alt="Amaria 6">
  
  </div>

  <div id="dfouaa" class="image-group">
    <img src="./picturces/dfouaa1.jpg" alt="Dfouaa 1">
    <img src="./picturces/dfouaa2.jpg" alt="Dfouaa 2">
    <img src="./picturces/dfouaa3.jpg" alt="Dfouaa 3">
    <img src="./picturces/dfouaa4.png" alt="Dfouaa 4">
  </div>

  <div id="neggafa" class="image-group">
    <img src="./picturces/neggafa1.jpg" alt="Neggafa 1">
    <img src="./picturces/neggafa2.jpg" alt="Neggafa 2">
    <img src="./picturces/neggafa3.jpg" alt="Neggafa 3">
    <img src="./picturces/neggafa4.jpg" alt="Neggafa 4">
    <img src="./picturces/neggafa5.jpg" alt="Neggafa 5">
  </div><br>
  <br>
  <div class="buttons">
    <button class="btn btn-gold"><a href="#"> Découvrir tous les prestations</a></button>
  
  </div>
  

  <div class="section">
    <h2>Nos Packs</h2>
    <p>Célébrez votre événements comme vous l'avez toujours rêvé !</p>

    <div class="buttons">
      <button class="btn btn-black"><a href="#">Découvrir Nos Packs</a> </button>
      <button class="btn btn-gold"><a href="#">Personnalisez Votre Pack</a></button>
    </div>
  </div>
    <a href="https://wa.me/212600000000" class="whatsapp">Contacter-Nous</a>

 <script>
  function showGroup(groupId) {
    const groups = document.querySelectorAll('.image-group');
    groups.forEach(group => {
      group.classList.remove('show');
      setTimeout(() => {
        group.style.display = 'none';
      }, 300);
    });

    const selectedGroup = document.getElementById(groupId);
    if (selectedGroup) {
      setTimeout(() => {
        selectedGroup.style.display = 'flex';
        selectedGroup.classList.add('show');
      }, 300);
    }

    const buttons = document.querySelectorAll('.nav-buttons button');
    buttons.forEach(btn => btn.classList.remove('active'));

    const activeBtn = [...buttons].find(btn =>
      btn.textContent.trim().toLowerCase().includes(groupId)
    );
    if (activeBtn) {
      activeBtn.classList.add('active');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    showGroup('hanaa');
  });
</script>

<?php include '../../footer.html'; ?>

</body>
</html>
