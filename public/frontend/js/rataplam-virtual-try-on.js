(function () {
  'use strict';

  var MAX_FILE_SIZE = 8 * 1024 * 1024;
  var DEFAULT_BODY_MAP = {
    torso_top: 0.245,
    torso_height: 0.355,
    shoulder_width: 0.42,
    chest_width: 0.40,
    waist_width: 0.34,
    hip_width: 0.44,
    garment_scale: 1,
    angles: [0, 45, 90, 135, 180, 225, 270, 315]
  };

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.rataplam-tryon-studio').forEach(initStudio);
  });

  function initStudio(studio) {
    var canvas = studio.querySelector('.rataplam-tryon-canvas');
    var ctx = canvas.getContext('2d');
    var empty = studio.querySelector('.rataplam-tryon-empty');
    var imagesScript = studio.querySelector('.rataplam-tryon-images');
    var images = parseImages(imagesScript);
    var primaryImage = studio.getAttribute('data-primary-image') || (images[0] && images[0].url) || '';
    var state = {
      childImage: null,
      garmentImage: null,
      garmentUrl: primaryImage,
      angle: 0,
      scaleX: 1,
      scaleY: 1,
      offsetY: 0,
      opacity: 0.92,
      bodyMap: Object.assign({}, DEFAULT_BODY_MAP),
      autoplay: null
    };

    bindPhotoUpload(studio, state, draw);
    bindGarments(studio, state, draw);
    bindControls(studio, state, draw);
    bindMeasures(studio, state, draw);
    bindActions(studio, state, draw);

    if (primaryImage) {
      loadImage(primaryImage).then(function (image) {
        state.garmentImage = image;
        draw();
      }).catch(function () {
        draw();
      });
    } else {
      draw();
    }

    function draw(targetAngle, targetCtx, targetCanvas, options) {
      var angle = typeof targetAngle === 'number' ? targetAngle : state.angle;
      var context = targetCtx || ctx;
      var activeCanvas = targetCanvas || canvas;
      var settings = options || {};
      var width = activeCanvas.width;
      var height = activeCanvas.height;

      context.clearRect(0, 0, width, height);
      drawBackground(context, width, height);

      if (state.childImage) {
        drawImageCover(context, state.childImage, 0, 0, width, height);
      } else {
        drawBodyGuide(context, width, height, state.bodyMap);
      }

      if (state.garmentImage) {
        drawGarment(context, state, angle, width, height, settings);
      }

      if (!settings.hideGuide) {
        drawAngleBadge(context, angle, width);
      }

      if (empty) {
        empty.classList.toggle('is-hidden', !!state.childImage);
      }
    }
  }

  function parseImages(script) {
    if (!script) {
      return [];
    }

    try {
      return JSON.parse(script.textContent || '[]');
    } catch (error) {
      return [];
    }
  }

  function bindPhotoUpload(studio, state, draw) {
    var input = studio.querySelector('[data-tryon-photo]');
    var dropzone = studio.querySelector('[data-tryon-dropzone]');
    var consent = studio.querySelector('[data-tryon-consent]');

    if (!input) {
      return;
    }

    input.addEventListener('change', function () {
      handleFile(input.files && input.files[0]);
    });

    if (dropzone) {
      ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
          event.preventDefault();
          dropzone.classList.add('is-dragover');
        });
      });

      ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
          event.preventDefault();
          dropzone.classList.remove('is-dragover');
        });
      });

      dropzone.addEventListener('drop', function (event) {
        var file = event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files[0];
        handleFile(file);
      });
    }

    function handleFile(file) {
      if (!file) {
        return;
      }

      if (consent && !consent.checked) {
        showResult(studio, 'Marque a autorizacao de uso da foto antes de enviar.', true);
        input.value = '';
        return;
      }

      if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
        showResult(studio, 'Use uma foto em JPG, PNG ou WEBP.', true);
        input.value = '';
        return;
      }

      if (file.size > MAX_FILE_SIZE) {
        showResult(studio, 'A foto precisa ter ate 8 MB.', true);
        input.value = '';
        return;
      }

      var reader = new FileReader();
      reader.onload = function (event) {
        loadImage(event.target.result).then(function (image) {
          state.childImage = image;
          showResult(studio, 'Foto carregada no navegador. Nenhum arquivo foi salvo no servidor.', false);
          draw();
        });
      };
      reader.readAsDataURL(file);
    }
  }

  function bindGarments(studio, state, draw) {
    var buttons = studio.querySelectorAll('[data-tryon-garment]');

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        buttons.forEach(function (item) {
          item.classList.remove('is-active');
        });
        button.classList.add('is-active');
        state.garmentUrl = button.getAttribute('data-tryon-garment');
        loadImage(state.garmentUrl).then(function (image) {
          state.garmentImage = image;
          draw();
        }).catch(function () {
          showResult(studio, 'Nao foi possivel carregar a imagem da peca selecionada.', true);
        });
      });
    });
  }

  function bindControls(studio, state, draw) {
    var angle = studio.querySelector('[data-tryon-angle]');
    var angleLabel = studio.querySelector('[data-tryon-angle-label]');
    var scaleX = studio.querySelector('[data-tryon-scale-x]');
    var scaleY = studio.querySelector('[data-tryon-scale-y]');
    var offsetY = studio.querySelector('[data-tryon-offset-y]');
    var opacity = studio.querySelector('[data-tryon-opacity]');
    var viewButtons = studio.querySelectorAll('[data-tryon-view]');
    var autoplay = studio.querySelector('[data-tryon-autoplay]');

    function setAngle(value) {
      state.angle = normalizeAngle(value);
      if (angle) {
        angle.value = String(state.angle);
      }
      if (angleLabel) {
        angleLabel.textContent = state.angle + ' graus';
      }
      viewButtons.forEach(function (button) {
        button.classList.toggle('is-active', viewToAngle(button.getAttribute('data-tryon-view')) === state.angle);
      });
      draw();
    }

    if (angle) {
      angle.addEventListener('input', function () {
        setAngle(parseInt(angle.value, 10) || 0);
      });
    }

    viewButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        stopAutoplay(state, autoplay);
        setAngle(viewToAngle(button.getAttribute('data-tryon-view')));
      });
    });

    if (autoplay) {
      autoplay.addEventListener('click', function () {
        if (state.autoplay) {
          stopAutoplay(state, autoplay);
          return;
        }

        autoplay.classList.add('is-active');
        state.autoplay = window.setInterval(function () {
          setAngle(state.angle + 4);
        }, 80);
      });
    }

    [
      [scaleX, 'scaleX', 100],
      [scaleY, 'scaleY', 100],
      [offsetY, 'offsetY', 1],
      [opacity, 'opacity', 100]
    ].forEach(function (entry) {
      var input = entry[0];
      var key = entry[1];
      var divisor = entry[2];

      if (!input) {
        return;
      }

      input.addEventListener('input', function () {
        state[key] = (parseFloat(input.value) || 0) / divisor;
        if (key === 'offsetY') {
          state[key] = parseFloat(input.value) || 0;
        }
        draw();
      });
    });
  }

  function bindMeasures(studio, state, draw) {
    var form = studio.querySelector('[data-tryon-measures]');

    if (!form) {
      return;
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value
        },
        body: new FormData(form)
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Falha ao calcular caimento.');
          }
          return response.json();
        })
        .then(function (data) {
          if (data.body_map) {
            state.bodyMap = Object.assign({}, DEFAULT_BODY_MAP, data.body_map);
          }
          showResult(studio, '<strong>' + escapeHtml(data.message || 'Caimento calculado.') + '</strong><br>' + escapeHtml(data.fit || ''), false, true);
          draw();
        })
        .catch(function () {
          showResult(studio, 'Nao foi possivel calcular o caimento agora.', true);
        });
    });
  }

  function bindActions(studio, state, draw) {
    var buildFrames = studio.querySelector('[data-tryon-build-frames]');
    var download = studio.querySelector('[data-tryon-download]');
    var frames = studio.querySelector('[data-tryon-frames]');
    var canvas = studio.querySelector('.rataplam-tryon-canvas');

    if (buildFrames && frames) {
      buildFrames.addEventListener('click', function () {
        frames.innerHTML = '';
        var angles = state.bodyMap.angles || DEFAULT_BODY_MAP.angles;
        angles.forEach(function (angle) {
          var frameCanvas = document.createElement('canvas');
          frameCanvas.width = canvas.width;
          frameCanvas.height = canvas.height;
          var frameCtx = frameCanvas.getContext('2d');
          draw(angle, frameCtx, frameCanvas, { hideGuide: true });

          try {
            var figure = document.createElement('div');
            var img = document.createElement('img');
            var label = document.createElement('span');
            figure.className = 'rataplam-tryon-frame';
            img.src = frameCanvas.toDataURL('image/png');
            img.alt = 'Angulo ' + angle + ' graus';
            label.textContent = angle + ' graus';
            figure.appendChild(img);
            figure.appendChild(label);
            frames.appendChild(figure);
          } catch (error) {
            showResult(studio, 'A geracao dos quadros 360 foi bloqueada pela origem de uma imagem.', true);
          }
        });
      });
    }

    if (download && canvas) {
      download.addEventListener('click', function () {
        try {
          canvas.toBlob(function (blob) {
            if (!blob) {
              showResult(studio, 'Nao foi possivel baixar a imagem do provador.', true);
              return;
            }
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'provador-rataplam.png';
            document.body.appendChild(link);
            link.click();
            URL.revokeObjectURL(link.href);
            link.remove();
          }, 'image/png');
        } catch (error) {
          showResult(studio, 'A imagem nao pode ser baixada por bloqueio de origem.', true);
        }
      });
    }
  }

  function drawBackground(ctx, width, height) {
    ctx.fillStyle = '#f8fafc';
    ctx.fillRect(0, 0, width, height);
    ctx.strokeStyle = 'rgba(148, 163, 184, 0.16)';
    ctx.lineWidth = 1;
    for (var x = 0; x < width; x += 60) {
      ctx.beginPath();
      ctx.moveTo(x, 0);
      ctx.lineTo(x, height);
      ctx.stroke();
    }
    for (var y = 0; y < height; y += 60) {
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(width, y);
      ctx.stroke();
    }
  }

  function drawBodyGuide(ctx, width, height, bodyMap) {
    var cx = width / 2;
    var top = height * 0.17;
    var head = width * 0.09;
    var shoulderY = height * bodyMap.torso_top;
    var torsoBottom = shoulderY + height * bodyMap.torso_height;

    ctx.save();
    ctx.strokeStyle = 'rgba(15, 118, 110, 0.36)';
    ctx.fillStyle = 'rgba(15, 118, 110, 0.06)';
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.arc(cx, top, head, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(cx - width * bodyMap.shoulder_width / 2, shoulderY);
    ctx.quadraticCurveTo(cx - width * 0.16, shoulderY + 140, cx - width * bodyMap.waist_width / 2, torsoBottom);
    ctx.lineTo(cx + width * bodyMap.waist_width / 2, torsoBottom);
    ctx.quadraticCurveTo(cx + width * 0.16, shoulderY + 140, cx + width * bodyMap.shoulder_width / 2, shoulderY);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();
    ctx.restore();
  }

  function drawGarment(ctx, state, angle, width, height) {
    var map = state.bodyMap;
    var rad = angle * Math.PI / 180;
    var perspective = Math.abs(Math.cos(rad));
    var widthFactor = 0.26 + perspective * 0.74;
    var sideShift = Math.sin(rad) * width * 0.045;
    var backView = angle > 90 && angle < 270;
    var cx = width / 2 + sideShift;
    var top = height * map.torso_top + state.offsetY;
    var garmentHeight = height * map.torso_height * map.garment_scale * state.scaleY;
    var shoulderWidth = width * map.shoulder_width * state.scaleX * widthFactor;
    var chestWidth = width * map.chest_width * state.scaleX * widthFactor;
    var waistWidth = width * map.waist_width * state.scaleX * widthFactor;
    var hipWidth = width * map.hip_width * state.scaleX * widthFactor;
    var bottom = top + garmentHeight;
    var imageRect = {
      x: cx - Math.max(shoulderWidth, hipWidth) * 0.62,
      y: top - garmentHeight * 0.06,
      w: Math.max(shoulderWidth, hipWidth) * 1.24,
      h: garmentHeight * 1.15
    };

    ctx.save();
    ctx.beginPath();
    ctx.moveTo(cx - shoulderWidth / 2, top);
    ctx.bezierCurveTo(cx - chestWidth / 2, top + garmentHeight * 0.25, cx - waistWidth / 2, top + garmentHeight * 0.55, cx - hipWidth / 2, bottom);
    ctx.lineTo(cx + hipWidth / 2, bottom);
    ctx.bezierCurveTo(cx + waistWidth / 2, top + garmentHeight * 0.55, cx + chestWidth / 2, top + garmentHeight * 0.25, cx + shoulderWidth / 2, top);
    ctx.quadraticCurveTo(cx, top + garmentHeight * 0.08, cx - shoulderWidth / 2, top);
    ctx.clip();

    ctx.globalAlpha = state.opacity * (backView ? 0.82 : 1);
    ctx.translate(cx, imageRect.y + imageRect.h / 2);
    ctx.transform(backView ? -1 : 1, 0, Math.sin(rad) * 0.08, 1, 0, 0);
    drawImageCover(ctx, state.garmentImage, -imageRect.w / 2, -imageRect.h / 2, imageRect.w, imageRect.h);
    ctx.restore();

    ctx.save();
    ctx.strokeStyle = backView ? 'rgba(15, 23, 42, 0.24)' : 'rgba(255, 255, 255, 0.42)';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(cx, top + 16);
    ctx.lineTo(cx, bottom - 12);
    ctx.stroke();
    ctx.strokeStyle = 'rgba(15, 118, 110, 0.34)';
    ctx.setLineDash([10, 8]);
    ctx.beginPath();
    ctx.moveTo(cx - shoulderWidth / 2, top);
    ctx.bezierCurveTo(cx - chestWidth / 2, top + garmentHeight * 0.25, cx - waistWidth / 2, top + garmentHeight * 0.55, cx - hipWidth / 2, bottom);
    ctx.lineTo(cx + hipWidth / 2, bottom);
    ctx.bezierCurveTo(cx + waistWidth / 2, top + garmentHeight * 0.55, cx + chestWidth / 2, top + garmentHeight * 0.25, cx + shoulderWidth / 2, top);
    ctx.stroke();
    ctx.restore();
  }

  function drawAngleBadge(ctx, angle, width) {
    ctx.save();
    ctx.fillStyle = 'rgba(17, 24, 39, 0.76)';
    ctx.fillRect(width - 160, 24, 128, 42);
    ctx.fillStyle = '#ffffff';
    ctx.font = '700 22px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(angle + ' graus', width - 96, 45);
    ctx.restore();
  }

  function drawImageCover(ctx, image, x, y, width, height) {
    var ratio = Math.max(width / image.width, height / image.height);
    var drawWidth = image.width * ratio;
    var drawHeight = image.height * ratio;
    var dx = x + (width - drawWidth) / 2;
    var dy = y + (height - drawHeight) / 2;
    ctx.drawImage(image, dx, dy, drawWidth, drawHeight);
  }

  function loadImage(src) {
    return new Promise(function (resolve, reject) {
      var image = new Image();
      image.onload = function () {
        resolve(image);
      };
      image.onerror = reject;
      image.src = src;
    });
  }

  function viewToAngle(view) {
    if (view === 'side') {
      return 90;
    }
    if (view === 'back') {
      return 180;
    }
    return 0;
  }

  function normalizeAngle(value) {
    var angle = value % 360;
    return angle < 0 ? angle + 360 : angle;
  }

  function stopAutoplay(state, button) {
    if (state.autoplay) {
      window.clearInterval(state.autoplay);
      state.autoplay = null;
    }
    if (button) {
      button.classList.remove('is-active');
    }
  }

  function showResult(studio, message, isError, isHtml) {
    var result = studio.querySelector('[data-tryon-result]');
    if (!result) {
      return;
    }

    result.classList.add('is-visible');
    result.style.borderLeftColor = isError ? '#dc2626' : '#0f766e';
    result.style.background = isError ? '#fef2f2' : '#ecfdf5';
    result.style.color = isError ? '#991b1b' : '#115e59';

    if (isHtml) {
      result.innerHTML = message;
    } else {
      result.textContent = message;
    }
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
})();
