<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>test de pruebas</title>
  </head>
  <body>
    <div class="container-sm">
      <h1>Envio test de prueba de nomina</h1>
      <div class="mb-3">
        <label for="nit" class="form-label">Nit</label>
        <input type="email" class="form-control" id="nit">
      </div>
      <div class="mb-3">
        <label for="digito_verificacion" class="form-label">Digito verificacion</label>
        <input type="email" class="form-control" id="digito_verificacion">
      </div>
      <div class="mb-3">
        <label for="client_token" class="form-label">Client Token</label>
        <input type="email" class="form-control" id="client_token">
      </div>
      <div class="mb-3">
        <label for="access_token" class="form-label">Access Token</label>
        <input type="email" class="form-control" id="access_token">
      </div>
      <div class="mb-3">
        <label for="consecutivo" class="form-label">consecutivo a iniciar las planillas</label>
        <input type="email" class="form-control" id="consecutivo" value="1">
      </div>
      <button type="button" class="btn btn-primary" onclick="payRoll()">Enviar nomina</button>

      <div class="accordion" id="accordionPayRoll">
      </div>


      <h4>Ajuste tipo 1</h4>
      <div class="mb-3">
        <label for="consecutivo_ajuste_1" class="form-label">consecutivo de la planilla de ajuste</label>
        <input type="email" class="form-control" id="consecutivo_ajuste_1">
      </div>
      <div class="mb-3">
        <label for="consecutivo_planilla_ajustar1" class="form-label">consecutivo de la planilla a ajustar</label>
        <input type="email" class="form-control" id="consecutivo_planilla_ajustar1">
      </div>
      <button type="button" class="btn btn-primary" onclick="payRollAdjustmentType1()">Enviar Ajuste (tipo 1)</button>
      <div class="accordion" id="accordionPayRollAdjustmentType1">
      </div>

      <h4>Ajuste tipo 2</h4>
      <div class="mb-3">
        <label for="consecutivo_ajuste_2" class="form-label">consecutivo de la planilla de ajuste</label>
        <input type="email" class="form-control" id="consecutivo_ajuste_2">
      </div>
      <div class="mb-3">
        <label for="consecutivo_planilla_ajustar2" class="form-label">consecutivo de la planilla a ajustar</label>
        <input type="email" class="form-control" id="consecutivo_planilla_ajustar2">
      </div>
      <button type="button" class="btn btn-primary" onclick="payRollAdjustmentType2()">Enviar Ajuste (tipo 2)</button>
      <div class="accordion" id="accordionPayRollAdjustmentType2">
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="json.js" ></script>
    <script src="index.js" ></script>
  </body>
</html>