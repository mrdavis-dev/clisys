<?php
// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
  header('Location: login.php');
  exit;

  $dni = $_SESSION['id'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Odontograma</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/main.css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.1/dist/html2canvas.min.js"></script>
	<script src="js/jquery-2.1.4.js"></script>
	<script src="js/angular.js"></script>
	<link rel="stylesheet" type="text/css" href="css/estilosOdontograma.css">
  <script src="https://cdn.rawgit.com/tsayen/dom-to-image/bfc00a6c5bba731027820199acd7b0a6e92149d8/dist/dom-to-image.min.js"></script>
</head>
<body ng-app="app">
<?php
include("menu.php");
?>

        <!-- Page Content  -->
      <div id="content" style="text-align:center;" class="p-4 p-md-5 pt-5">
        <div class=" animated fadeIn container-fluid centrar p-1 mb-3 border-bottom">
          <h1 class="display-3">Odontograma</h1>
        </div>
        <div class="container-fluid justify-content-center bg-white" id="contenedor" ng-controller="dientes">
            <div class="container text-center">
              <center>
                <div>
                  <div class="container justify-content-center">
                    <span class="numbers ">18</span>
                    <span class="numbers ">17</span>
                    <span class="numbers ">16</span>
                    <span class="numbers ">15</span>
                    <span class="numbers ">14</span>
                    <span class="numbers ">13</span>
                    <span class="numbers ">12</span>
                    <span class="numbers ">11</span>
                    <span class="numbers ">21</span>
                    <span class="numbers ">22</span>
                    <span class="numbers ">23</span>
                    <span class="numbers ">24</span>
                    <span class="numbers ">25</span>
                    <span class="numbers ">26</span>
                    <span class="numbers ">27</span>
                    <span class="numbers ">28</span>
                  </div>
                  <svg height="50" class="{{i.tipoDiente}}" width="50"  data-ng-repeat="i in adultoArriva" id="{{i.id}}">
                      <polygon points="10,15 15,10 50,45 45,50" estado="4" value="6" class="ausente" />
                      <polygon points="45,10 50,15 15,50 10,45" estado="4" value="7" class="ausente" />
                      <circle cx="30" cy="30" r="16" estado="8" value="8" class="corona"/>
                      <circle cx="30" cy="30" r="20" estado="3" value="9" class="endodoncia"/>
                      <polygon points="50,10 40,10 10,26 10,32 46,32 10,50 20,50 50,36 50,28 14,28" estado="6" value="10" class="implante"/>
                      <polygon points="10,10 50,10 40,20 20,20" estado="0" value="1" class="diente" />
                      <polygon points="50,10 50,50 40,40 40,20" estado="0" value="2" class="diente" />
                      <polygon points="50,50 10,50 20,40 40,40" estado="0" value="3" class="diente" />
                      <polygon points="10,50 20,40 20,20 10,10" estado="0" value="4" class="diente" />
                      <polygon points="20,20 40,20 40,40 20,40" estado="0" value="5" class="diente" />

                  </svg>
                </div>
                <div>
                  <div class="container-fluid mt-2 justify-content-center">
                    <span class="numbers  ">55</span>
                    <span class="numbers  ">54</span>
                    <span class="numbers  ">53</span>
                    <span class="numbers  ">52</span>
                    <span class="numbers  ">51</span>
                    <span class="numbers  ">61</span>
                    <span class="numbers  ">62</span>
                    <span class="numbers  ">63</span>
                    <span class="numbers  ">64</span>
                    <span class="numbers  ">65</span>
                  </div>
                  <svg height="50" class="{{i.tipoDiente}}" width="50"  data-ng-repeat="i in ninoArriva" id="{{i.id}}">
                      <polygon points="10,15 15,10 50,45 45,50" estado="4" value="6" class="ausente" />
                      <polygon points="45,10 50,15 15,50 10,45" estado="4" value="7" class="ausente" />
                      <circle cx="30" cy="30" r="16" estado="8" value="8" class="corona"/>
                      <circle cx="30" cy="30" r="20" estado="3" value="9" class="endodoncia"/>
                      <polygon points="50,10 40,10 10,26 10,32 46,32 10,50 20,50 50,36 50,28 14,28" estado="6" value="10" class="implante"/>
                      <polygon points="10,10 50,10 40,20 20,20" estado="0" value="1" class="diente" />
                      <polygon points="50,10 50,50 40,40 40,20" estado="0" value="2" class="diente" />
                      <polygon points="50,50 10,50 20,40 40,40" estado="0" value="3" class="diente" />
                      <polygon points="10,50 20,40 20,20 10,10" estado="0" value="4" class="diente" />
                      <polygon points="20,20 40,20 40,40 20,40" estado="0" value="5" class="diente" />

                  </svg>
                </div>
                <div>

                  <svg height="50" class="{{i.tipoDiente}}" width="50"  data-ng-repeat="i in ninoAbajo" id="{{i.id}}">
                      <polygon points="10,15 15,10 50,45 45,50" estado="4" value="6" class="ausente" />
                      <polygon points="45,10 50,15 15,50 10,45" estado="4" value="7" class="ausente" />
                      <circle cx="30" cy="30" r="16" estado="8" value="8" class="corona"/>
                      <circle cx="30" cy="30" r="20" estado="3" value="9" class="endodoncia"/>
                      <polygon points="50,10 40,10 10,26 10,32 46,32 10,50 20,50 50,36 50,28 14,28" estado="6" value="10" class="implante"/>
                      <polygon points="10,10 50,10 40,20 20,20" estado="0" value="1" class="diente" />
                      <polygon points="50,10 50,50 40,40 40,20" estado="0" value="2" class="diente" />
                      <polygon points="50,50 10,50 20,40 40,40" estado="0" value="3" class="diente" />
                      <polygon points="10,50 20,40 20,20 10,10" estado="0" value="4" class="diente" />
                      <polygon points="20,20 40,20 40,40 20,40" estado="0" value="5" class="diente" />

                  </svg>
                  <div class="container-fluid mt-2 justify-content-center">
                    <span class="numbers  ">85</span>
                    <span class="numbers  ">84</span>
                    <span class="numbers  ">83</span>
                    <span class="numbers  ">82</span>
                    <span class="numbers  ">81</span>
                    <span class="numbers  ">71</span>
                    <span class="numbers  ">72</span>
                    <span class="numbers  ">73</span>
                    <span class="numbers  ">74</span>
                    <span class="numbers  ">75</span>
                  </div>
                </div>
                <div>
                  <svg height="50" class="{{i.tipoDiente}}" width="50"  data-ng-repeat="i in adultoAbajo" id="{{i.id}}">
                       <polygon points="10,15 15,10 50,45 45,50" estado="4" value="6" class="ausente" />
                      <polygon points="45,10 50,15 15,50 10,45" estado="4" value="7" class="ausente" />
                      <circle cx="30" cy="30" r="16" estado="8" value="8" class="corona"/>
                      <circle cx="30" cy="30" r="20" estado="3" value="9" class="endodoncia"/>
                      <polygon points="50,10 40,10 10,26 10,32 46,32 10,50 20,50 50,36 50,28 14,28" estado="6" value="10" class="implante"/>
                      <polygon points="10,10 50,10 40,20 20,20" estado="0" value="1" class="diente" />
                      <polygon points="50,10 50,50 40,40 40,20" estado="0" value="2" class="diente" />
                      <polygon points="50,50 10,50 20,40 40,40" estado="0" value="3" class="diente" />
                      <polygon points="10,50 20,40 20,20 10,10" estado="0" value="4" class="diente" />
                      <polygon points="20,20 40,20 40,40 20,40" estado="0" value="5" class="diente" />

                  </svg>
                  <div class="container mt-2 justify-content-center">
                    <span class="numbers2 ">48</span>
                    <span class="numbers2 ">47</span>
                    <span class="numbers2 ">46</span>
                    <span class="numbers2 ">45</span>
                    <span class="numbers2">44</span>
                    <span class="numbers2 ">43</span>
                    <span class="numbers2 ">42</span>
                    <span class="numbers2 ">41</span>
                    <span class="numbers2 ">31</span>
                    <span class="numbers2 ">32</span>
                    <span class="numbers2 ">33</span>
                    <span class="numbers2 ">34</span>
                    <span class="numbers2 ">35</span>
                    <span class="numbers2 ">36</span>
                    <span class="numbers2 ">37</span>
                    <span class="numbers2 ">38</span>
                  </div>
                </div>
              </center>
            </div>
        </div>
        <br>

        <div class="container">

          <table class="table justify-content-center" align="center">
            <tr>
              <th>Amalgama</th>
              <th>Caries</th>
              <th>Endodoncia</th>
              <th>Ausente</th>
              <th>Resina</th>
              <th>Implante</th>
              <th>Sellante</th>
              <th>Corona</th>
              <th>Obturación</th>
            </tr>
              <td><center><div class="color container" value="1" style="background-color:blue;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="2" style="background-color:red;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="3" style="background-color:orange;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="4" style="background-color:black;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="5" style="background-color:blue;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="6" style="background-color:#CC66CC;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="7" style="background-color:blue;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="8" style="background-color:#CC6600;width:20px;height:20px"></div></center></td>
              <td><center><div class="color container" value="9" style="background-color:blue;width:20px;height:20px"></div></center></td>
            <tr>
          </table>

        </div>

				<div class="container centrar mt-2 mb-3">
					<!-- <input type="button" class="btn btn-primary m-2" value="ver" id="ver"/> -->
					<!-- <input type="file" class="btn btn-primary m-2" name="add" value="Agregar" /> -->
          <script type="text/javascript">
            function limpiar() {
              $('#contenedor').load(document.URL +  ' #contenedor');
            }
          </script>
					<input type="button" class="btn btn-primary m-2" onclick="limpiar()" name="limpiar" value="limpiar" />
          <input type="button" class="cap btn btn-primary m-2" onclick="cap" value="Descargar">
				</div>

				<div class="container-fluid centrar mt-2">
					<input type="radio" class="m-2" id="Decidua" name="tipo" value="1" checked />Permanente
					<input type="radio" class="m-2" id="Niños" name="tipo" value="2" />Decidua
					<input type="radio" class="m-2" id="Mixta" name="tipo" value="3" />Mixta
				</div>

			<div class="container" style="margin-top: 40px;">



        <!-- text box para observaciones -->
        <div class="row  mb-3 justify-content-center">
				<div class="col-8 container centrar pt-3 row">
					<div class="col-3">
            <!--  -->
            <form action="">
              <span  class="btn-block btn btn-primary align-middle shadow" >Buscar</span>
            </form>

					</div>
					<div class="col-9">
            <!-- insert desordenado entre div -->
            <form name="frmImage" enctype="multipart/form-data" action="insert_odo.php" method="post">
						<input class="form-control border" type="text" name="search" id="search_text" placeholder="cédula">
					</div>
				</div>
		</div>
		<div class="container-fluid text-center">
        <div class="container-fluid text-center">
          <textarea class="border rounded p-2" name="consul" rows="4" cols="80" placeholder="Observaciones de la consulta..." ></textarea>
        </div>
        <div class="container">
          <input class="btn btn-primary m-2" name="userImage" type="file">
          <button class="btn btn-primary m-2" type="submit" name="submit">Guardar consulta</button>
        </div>

      </form>
		</div>

				<div class="container m-2 p-2">
					<h1>Información de paciente:</h1>
				</div>

<div id="result"></div>
<script>
  $(document).ready(function(){

  load_data();

  function load_data(query)
  {
  $.ajax({
  url:"consulta_odo.php",
  method:"POST",
  data:{query:query},
  success:function(data)
  {
    $('#result').html(data);
  }
  });
  }
  $('#search_text').keyup(function(){
  var search = $(this).val();
  if(search != '')
  {
  load_data(search);
  }
  else
  {
  load_data();
  }
  });
  });
</script>
			</div>
        </div>
	</div>

  <!-- domtoimage function -->
    <script type="text/javascript">
      // function screen(){
      // const render = node =>
      //   domtoimage.toPng(node)
      //   .then(dataUrl => {
      //   console.log(performance.now()-pf)
      //     const img = new Image();
      //     img.src = dataUrl;
      //     console.log(dataUrl);
      //     $('#content').append(img);
      //     })
      //   .catch(error =>
      //     console.error('oops, something went wrong!', error)
      //   );
      //
      // const foo = document.getElementById('contenedor');
      //
      // var pf=performance.now();
      // render(foo);
      //}
      // console.log(dataurl);
    </script>

	<!-- MODAL DE INFORMACION DE PACIENTE -->
	<div class="modal fade bg-dark" id="Mymodal-1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Información adicional</h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>

			<form action="post">
			<div class="modal-body ">

			  <div class="container">
				<legend for="id_motivo"><h5>Motivo de la visita</h5></legend>
				<textarea name="" class="m-1 form-control border border-primary" id="id_motivo" disabled="true" cols="40" rows="10"></textarea>

				<legend for="id_habitos"><h5>Habitos higienicos</h5></legend>
				<textarea name="" class="m-1 form-control border border-primary" disabled="true" id="id_habitos" cols="40" rows="10"></textarea>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Está bajo tratamiento médico actualmente?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Ha sido hospitalizado quirúrgicamente?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Esta tomando algún medicamento o droga?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Presenta algún tipo de alergia?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Ha tenido algún tipo de enfermedad cardiaca?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Es usted diabético o alguno de sus familiares la padece o padeció?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Ha tenido tubérculosis o hepatitis?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Ha presentado alteraciones en el sangrado?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Ha tenido algúna enfermedad de transmisión sexual?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			  <div class="container m-1 p-1">
				<legend><h5>¿Tiene algún tipo de mal hábito?</h5></legend>
				<label for=""> <h6>Respuesta</h6></label>
			  </div>

			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
			</form>
		  </div>
		</div>
	  </div>


<script type="text/javascript">

!(function (a) {
  "use strict";
  function b(a, b) {
    function c(a) {
      return (
        b.bgcolor && (a.style.backgroundColor = b.bgcolor),
        b.width && (a.style.width = b.width + "px"),
        b.height && (a.style.height = b.height + "px"),
        b.style &&
          Object.keys(b.style).forEach(function (c) {
            a.style[c] = b.style[c];
          }),
        a
      );
    }
    return (
      (b = b || {}),
      Promise.resolve(a)
        .then(function (a) {
          return h(a, b.filter, !0);
        })
        .then(i)
        .then(j)
        .then(c)
        .then(function (c) {
          return k(c, b.width || p.width(a), b.height || p.height(a));
        })
    );
  }
  function c(a, b) {
    return g(a, b || {}).then(function (b) {
      return b
        .getContext("2d")
        .getImageData(0, 0, p.width(a), p.height(a)).data;
    });
  }
  function d(a, b) {
    return g(a, b || {}).then(function (a) {
      return a.toDataURL();
    });
  }
  function e(a, b) {
    return (
      (b = b || {}),
      g(a, b).then(function (a) {
        return a.toDataURL("image/jpeg", b.quality || 1);
      })
    );
  }
  function f(a, b) {
    return g(a, b || {}).then(p.canvasToBlob);
  }
  function g(a, c) {
    function d(a) {
      var b = document.createElement("canvas");
      if (
        ((b.width = c.width || p.width(a)),
        (b.height = c.height || p.height(a)),
        c.bgcolor)
      ) {
        var d = b.getContext("2d");
        (d.fillStyle = c.bgcolor), d.fillRect(0, 0, b.width, b.height);
      }
      return b;
    }
    return b(a, c)
      .then(p.makeImage)
      .then(p.delay(100))
      .then(function (b) {
        var c = d(a);
        return c.getContext("2d").drawImage(b, 0, 0), c;
      });
  }
  function h(a, b, c) {
    function d(a) {
      return a instanceof HTMLCanvasElement
        ? p.makeImage(a.toDataURL())
        : a.cloneNode(!1);
    }
    function e(a, b, c) {
      function d(a, b, c) {
        var d = Promise.resolve();
        return (
          b.forEach(function (b) {
            d = d
              .then(function () {
                return h(b, c);
              })
              .then(function (b) {
                b && a.appendChild(b);
              });
          }),
          d
        );
      }
      var e = a.childNodes;
      return 0 === e.length
        ? Promise.resolve(b)
        : d(b, p.asArray(e), c).then(function () {
            return b;
          });
    }
    function f(a, b) {
      function c() {
        function c(a, b) {
          function c(a, b) {
            p.asArray(a).forEach(function (c) {
              b.setProperty(c, a.getPropertyValue(c), a.getPropertyPriority(c));
            });
          }
          a.cssText ? (b.cssText = a.cssText) : c(a, b);
        }
        c(window.getComputedStyle(a), b.style);
      }
      function d() {
        function c(c) {
          function d(a, b, c) {
            function d(a) {
              var b = a.getPropertyValue("content");
              return a.cssText + " content: " + b + ";";
            }
            function e(a) {
              function b(b) {
                return (
                  b +
                  ": " +
                  a.getPropertyValue(b) +
                  (a.getPropertyPriority(b) ? " !important" : "")
                );
              }
              return p.asArray(a).map(b).join("; ") + ";";
            }
            var f = "." + a + ":" + b,
              g = c.cssText ? d(c) : e(c);
            return document.createTextNode(f + "{" + g + "}");
          }
          var e = window.getComputedStyle(a, c),
            f = e.getPropertyValue("content");
          if ("" !== f && "none" !== f) {
            var g = p.uid();
            b.className = b.className + " " + g;
            var h = document.createElement("style");
            h.appendChild(d(g, c, e)), b.appendChild(h);
          }
        }
        [":before", ":after"].forEach(function (a) {
          c(a);
        });
      }
      function e() {
        a instanceof HTMLTextAreaElement && (b.innerHTML = a.value),
          a instanceof HTMLInputElement && b.setAttribute("value", a.value);
      }
      function f() {
        b instanceof SVGElement &&
          (b.setAttribute("xmlns", "http://www.w3.org/2000/svg"),
          b instanceof SVGRectElement &&
            ["width", "height"].forEach(function (a) {
              var c = b.getAttribute(a);
              c && b.style.setProperty(a, c);
            }));
      }
      return b instanceof Element
        ? Promise.resolve()
            .then(c)
            .then(d)
            .then(e)
            .then(f)
            .then(function () {
              return b;
            })
        : b;
    }
    return c || !b || b(a)
      ? Promise.resolve(a)
          .then(d)
          .then(function (c) {
            return e(a, c, b);
          })
          .then(function (b) {
            return f(a, b);
          })
      : Promise.resolve();
  }
  function i(a) {
    return r.resolveAll().then(function (b) {
      var c = document.createElement("style");
      return a.appendChild(c), c.appendChild(document.createTextNode(b)), a;
    });
  }
  function j(a) {
    return s.inlineAll(a).then(function () {
      return a;
    });
  }
  function k(a, b, c) {
    return Promise.resolve(a)
      .then(function (a) {
        return (
          a.setAttribute("xmlns", "http://www.w3.org/1999/xhtml"),
          new XMLSerializer().serializeToString(a)
        );
      })
      .then(p.escapeXhtml)
      .then(function (a) {
        return (
          '<foreignObject x="0" y="0" width="100%" height="100%">' +
          a +
          "</foreignObject>"
        );
      })
      .then(function (a) {
        return (
          '<svg xmlns="http://www.w3.org/2000/svg" width="' +
          b +
          '" height="' +
          c +
          '">' +
          a +
          "</svg>"
        );
      })
      .then(function (a) {
        return "data:image/svg+xml;charset=utf-8," + a;
      });
  }
  function l() {
    function a() {
      var a = "application/font-woff",
        b = "image/jpeg";
      return {
        woff: a,
        woff2: a,
        ttf: "application/font-truetype",
        eot: "application/vnd.ms-fontobject",
        png: "image/png",
        jpg: b,
        jpeg: b,
        gif: "image/gif",
        tiff: "image/tiff",
        svg: "image/svg+xml"
      };
    }
    function b(a) {
      var b = /\.([^\.\/]*?)$/g.exec(a);
      return b ? b[1] : "";
    }
    function c(c) {
      var d = b(c).toLowerCase();
      return a()[d] || "";
    }
    function d(a) {
      return a.search(/^(data:)/) !== -1;
    }
    function e(a) {
      return new Promise(function (b) {
        for (
          var c = window.atob(a.toDataURL().split(",")[1]),
            d = c.length,
            e = new Uint8Array(d),
            f = 0;
          f < d;
          f++
        )
          e[f] = c.charCodeAt(f);
        b(new Blob([e], { type: "image/png" }));
      });
    }
    function f(a) {
      return a.toBlob
        ? new Promise(function (b) {
            a.toBlob(b);
          })
        : e(a);
    }
    function g(a, b) {
      var c = document.implementation.createHTMLDocument(),
        d = c.createElement("base");
      c.head.appendChild(d);
      var e = c.createElement("a");
      return c.body.appendChild(e), (d.href = b), (e.href = a), e.href;
    }
    function h() {
      var a = 0;
      return function () {
        function b() {
          return (
            "0000" + ((Math.random() * Math.pow(36, 4)) << 0).toString(36)
          ).slice(-4);
        }
        return "u" + b() + a++;
      };
    }
    function i(a) {
      return new Promise(function (b, c) {
        var d = new Image();
        (d.onload = function () {
          b(d);
        }),
          (d.onerror = c),
          (d.src = a);
      });
    }
    function j(a) {
      var b = 3e4;
      return new Promise(function (c) {
        function d() {
          if (4 === g.readyState) {
            if (200 !== g.status)
              return void f(
                "cannot fetch resource: " + a + ", status: " + g.status
              );
            var b = new FileReader();
            (b.onloadend = function () {
              var a = b.result.split(/,/)[1];
              c(a);
            }),
              b.readAsDataURL(g.response);
          }
        }
        function e() {
          f("timeout of " + b + "ms occured while fetching resource: " + a);
        }
        function f(a) {
          console.error(a), c("");
        }
        var g = new XMLHttpRequest();
        (g.onreadystatechange = d),
          (g.ontimeout = e),
          (g.responseType = "blob"),
          (g.timeout = b),
          g.open("GET", a, !0),
          g.send();
      });
    }
    function k(a, b) {
      return "data:" + b + ";base64," + a;
    }
    function l(a) {
      return a.replace(/([.*+?^${}()|\[\]\/\\])/g, "\\$1");
    }
    function m(a) {
      return function (b) {
        return new Promise(function (c) {
          setTimeout(function () {
            c(b);
          }, a);
        });
      };
    }
    function n(a) {
      for (var b = [], c = a.length, d = 0; d < c; d++) b.push(a[d]);
      return b;
    }
    function o(a) {
      return a.replace(/#/g, "%23").replace(/\n/g, "%0A");
    }
    function p(a) {
      var b = r(a, "border-left-width"),
        c = r(a, "border-right-width");
      return a.scrollWidth + b + c;
    }
    function q(a) {
      var b = r(a, "border-top-width"),
        c = r(a, "border-bottom-width");
      return a.scrollHeight + b + c;
    }
    function r(a, b) {
      var c = window.getComputedStyle(a).getPropertyValue(b);
      return parseFloat(c.replace("px", ""));
    }
    return {
      escape: l,
      parseExtension: b,
      mimeType: c,
      dataAsUrl: k,
      isDataUrl: d,
      canvasToBlob: f,
      resolveUrl: g,
      getAndEncode: j,
      uid: h(),
      delay: m,
      asArray: n,
      escapeXhtml: o,
      makeImage: i,
      width: p,
      height: q
    };
  }
  function m() {
    function a(a) {
      return a.search(e) !== -1;
    }
    function b(a) {
      for (var b, c = []; null !== (b = e.exec(a)); ) c.push(b[1]);
      return c.filter(function (a) {
        return !p.isDataUrl(a);
      });
    }
    function c(a, b, c, d) {
      function e(a) {
        return new RegExp(
          "(url\\(['\"]?)(" + p.escape(a) + ")(['\"]?\\))",
          "g"
        );
      }
      return Promise.resolve(b)
        .then(function (a) {
          return c ? p.resolveUrl(a, c) : a;
        })
        .then(d || p.getAndEncode)
        .then(function (a) {
          return p.dataAsUrl(a, p.mimeType(b));
        })
        .then(function (c) {
          return a.replace(e(b), "$1" + c + "$3");
        });
    }
    function d(d, e, f) {
      function g() {
        return !a(d);
      }
      return g()
        ? Promise.resolve(d)
        : Promise.resolve(d)
            .then(b)
            .then(function (a) {
              var b = Promise.resolve(d);
              return (
                a.forEach(function (a) {
                  b = b.then(function (b) {
                    return c(b, a, e, f);
                  });
                }),
                b
              );
            });
    }
    var e = /url\(['"]?([^'"]+?)['"]?\)/g;
    return { inlineAll: d, shouldProcess: a, impl: { readUrls: b, inline: c } };
  }
  function n() {
    function a() {
      return b(document)
        .then(function (a) {
          return Promise.all(
            a.map(function (a) {
              return a.resolve();
            })
          );
        })
        .then(function (a) {
          return a.join("\n");
        });
    }
    function b() {
      function a(a) {
        return a
          .filter(function (a) {
            return a.type === CSSRule.FONT_FACE_RULE;
          })
          .filter(function (a) {
            return q.shouldProcess(a.style.getPropertyValue("src"));
          });
      }
      function b(a) {
        var b = [];
        return (
          a.forEach(function (a) {
            try {
              p.asArray(a.cssRules || []).forEach(b.push.bind(b));
            } catch (c) {
              console.log(
                "Error while reading CSS rules from " + a.href,
                c.toString()
              );
            }
          }),
          b
        );
      }
      function c(a) {
        return {
          resolve: function () {
            var b = (a.parentStyleSheet || {}).href;
            return q.inlineAll(a.cssText, b);
          },
          src: function () {
            return a.style.getPropertyValue("src");
          }
        };
      }
      return Promise.resolve(p.asArray(document.styleSheets))
        .then(b)
        .then(a)
        .then(function (a) {
          return a.map(c);
        });
    }
    return { resolveAll: a, impl: { readAll: b } };
  }
  function o() {
    function a(a) {
      function b(b) {
        return p.isDataUrl(a.src)
          ? Promise.resolve()
          : Promise.resolve(a.src)
              .then(b || p.getAndEncode)
              .then(function (b) {
                return p.dataAsUrl(b, p.mimeType(a.src));
              })
              .then(function (b) {
                return new Promise(function (c, d) {
                  (a.onload = c), (a.onerror = d), (a.src = b);
                });
              });
      }
      return { inline: b };
    }
    function b(c) {
      function d(a) {
        var b = a.style.getPropertyValue("background");
        return b
          ? q
              .inlineAll(b)
              .then(function (b) {
                a.style.setProperty(
                  "background",
                  b,
                  a.style.getPropertyPriority("background")
                );
              })
              .then(function () {
                return a;
              })
          : Promise.resolve(a);
      }
      return c instanceof Element
        ? d(c).then(function () {
            return c instanceof HTMLImageElement
              ? a(c).inline()
              : Promise.all(
                  p.asArray(c.childNodes).map(function (a) {
                    return b(a);
                  })
                );
          })
        : Promise.resolve(c);
    }
    return { inlineAll: b, impl: { newImage: a } };
  }
  var p = l(),
    q = m(),
    r = n(),
    s = o(),
    t = {
      toSvg: b,
      toPng: d,
      toJpeg: e,
      toBlob: f,
      toPixelData: c,
      impl: { fontFaces: r, images: s, util: p, inliner: q }
    };
  "undefined" != typeof module ? (module.exports = t) : (a.domtoimage = t);
})(this);

  $(document).ready(function () {
    $(".cap").on("click", function () {
      domtoimage
        .toJpeg(document.getElementById("contenedor"), { quality: 0.15 })
        .then(function (dataUrl) {
          var link = document.createElement("a");
          link.download = "odontograma.jpeg";
          link.href = dataUrl;
          link.click();
        });
    });
  });



</script>

<script src="js/main.js"></script>
<!-- jquery/javascript -->
<script type="text/javascript" src="js/jquery-odontograma.js"></script>
<!--Modulo de Angular-->
<script type="text/javascript" src="js/app.js"></script>
<!-- Angular Directivas-->
<script type="text/javascript" src="js/controller.js"></script>
</body>
</html>
