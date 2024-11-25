<html lang="en">
<head>
  <title>Three.js API Stars with Labels</title>
  <meta charset="utf-8">
</head>
<body style="margin:0px;">
  <script async src="https://unpkg.com/es-module-shims@1.3.6/dist/es-module-shims.js"></script>

  <script type="importmap">
    {
      "imports": {
        "three": "https://unpkg.com/three@0.138.3/build/three.module.js"
      }
    }
  </script>

  <script type="module">
    import * as THREE from 'three';
    import { FontLoader } from 'https://unpkg.com/three@0.138.3/examples/jsm/loaders/FontLoader.js';
    import { TextGeometry } from 'https://unpkg.com/three@0.138.3/examples/jsm/geometries/TextGeometry.js';

    let camera, scene, renderer, stars = [];
    const moveSpeed = 10;
    const rotationSpeed = 0.05;
    const rollSpeed = 0.05; // Speed of rotation around the Z-axis
    const minScale = 0.1;
    const labelDistanceThreshold = 500;
    let isMoving = false;

    async function fetchStars() {
      const response = await fetch('http://127.0.0.1:8000/api/v1/stars/list');
      const data = await response.json();
      return data;
    }

    function getColorByType(type) {
      const colors = {
        "Brown Dwarf": 0x8B4513,
        "Supergiant": 0xFFD700,
        "Neutron Star": 0xFF4500,
        "Red Supergiant": 0xE4080A,
        'Yellow Dwarf': 0xFFECA1, 
        'Red Giant': 0xE41A50, 
        'Blue Giant': 0x060270, 
        'White Dwarf': 0xFFFFFF, 
        'Neutron Star': 0xCC6CE7, 
        'Pulsar': 0x590171, 
        'Supergiant': 0xFFFFFF,
        "Default": 0xFFFFFF
      };
      return colors[type] || colors["Default"];
    }

    function init() {
      camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 2000);
      camera.position.z = 500;

      scene = new THREE.Scene();

      renderer = new THREE.WebGLRenderer();
      renderer.setSize(window.innerWidth, window.innerHeight);
      document.body.appendChild(renderer.domElement);

      window.addEventListener('keydown', onKeyDown, false);
      window.addEventListener('keyup', onKeyUp, false);

      const loader = new FontLoader();
      loader.load('https://unpkg.com/three@0.138.3/examples/fonts/helvetiker_regular.typeface.json', function (font) {
        fetchStars().then(starData => {
          addStars(starData, font);
          render();
        });
      });

      // Add ambient light for the gradient effect
      const ambientLight = new THREE.AmbientLight(0xffffff, 0.3);
      scene.add(ambientLight);

      // Add point light to enhance the glow effect
      const pointLight = new THREE.PointLight(0xffffff, 1, 2000);
      pointLight.position.set(0, 0, 500);
      scene.add(pointLight);
    }

    function addStars(starData, font) {
      starData.forEach(star => {
        const mainSphereGeometry = new THREE.SphereGeometry(25, 32, 32);
        const auraSphereGeometry = new THREE.SphereGeometry(30, 32, 32);

        const color = getColorByType(star.type);

        // Main star material
        const mainMaterial = new THREE.MeshPhongMaterial({ color, emissive: color, emissiveIntensity: 0.5 });

        // Aura material with transparency
        const auraMaterial = new THREE.MeshBasicMaterial({
          color,
          transparent: true,
          opacity: 0.3
        });

        const mainSphere = new THREE.Mesh(mainSphereGeometry, mainMaterial);
        const auraSphere = new THREE.Mesh(auraSphereGeometry, auraMaterial);

        mainSphere.position.set(star.x, star.y, star.z);
        auraSphere.position.copy(mainSphere.position);

        scene.add(mainSphere);
        scene.add(auraSphere);

        const label = createTextLabel(star.name, font);
        label.position.set(star.x, star.y + 50, star.z);
        scene.add(label);

        stars.push({ sphere: mainSphere, aura: auraSphere, label, position: new THREE.Vector3(star.x, star.y, star.z) });
      });
    }

    function createTextLabel(text, font) {
      const geometry = new TextGeometry(text, {
        font: font,
        size: 5,
        height: 1
      });

      const material = new THREE.MeshBasicMaterial({ color: 0xffffff });
      const mesh = new THREE.Mesh(geometry, material);

      return mesh;
    }

    function updateStarScalesAndLabels() {
      const frustum = new THREE.Frustum();
      const cameraViewProjectionMatrix = new THREE.Matrix4();

      cameraViewProjectionMatrix.multiplyMatrices(camera.projectionMatrix, camera.matrixWorldInverse);
      frustum.setFromMatrix(cameraViewProjectionMatrix);

      stars.forEach(({ sphere, aura, label, position }) => {
        const distance = camera.position.distanceTo(position);
        const scale = Math.max(1 / (distance * 0.05), minScale);
        sphere.scale.set(scale, scale, scale);
        aura.scale.set(scale, scale, scale);

        const isVisible = frustum.containsPoint(position);
        label.visible = isVisible && distance <= labelDistanceThreshold;

        if (label.visible) {
          label.lookAt(camera.position);
        }
      });
    }

    function onKeyDown(event) {
      switch (event.key.toLowerCase()) {
        case 'w':
          isMoving = true;
          moveCamera(true);
          break;
        case 's':
          isMoving = true;
          moveCamera(false);
          break;
        case 'arrowup':
          camera.rotation.x -= rotationSpeed;
          break;
        case 'arrowdown':
          camera.rotation.x += rotationSpeed;
          break;
        case 'arrowleft':
          camera.rotation.y -= rotationSpeed;
          break;
        case 'arrowright':
          camera.rotation.y += rotationSpeed;
          break;
        case 'q': // Rotate around Z-axis counter-clockwise
          camera.rotation.z += rollSpeed;
          break;
        case 'e': // Rotate around Z-axis clockwise
          camera.rotation.z -= rollSpeed;
          break;
      }
    }

    function onKeyUp(event) {
      if (['w', 's'].includes(event.key.toLowerCase())) {
        isMoving = false;
      }
    }

    function moveCamera(forward) {
      if (!isMoving) return;

      const direction = new THREE.Vector3();
      camera.getWorldDirection(direction);
      direction.normalize();
      const velocity = direction.multiplyScalar(moveSpeed);
      if (!forward) velocity.negate();
      camera.position.add(velocity);
    }

    function render() {
      requestAnimationFrame(render);
      updateStarScalesAndLabels();
      renderer.render(scene, camera);
    }

    init();
  </script>
</body>
</html>
