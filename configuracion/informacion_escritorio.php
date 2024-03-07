  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><img src="../temas/clasico/images/logo.gif" width="287" height="63"></td>
    </tr>
    <tr>
      <td><table align="center" cellpadding="0" cellspacing="0" class="FUENTE11BL">
        <TR>
          <TD colspan="2">&nbsp;</TD>
        </TR>
        <TR>
          <TD colspan="2" align="center">
            Sistema Integrado de Informacion de Plataforma
          </TD>
        </TR>
        <TR>
          <TD colspan="2"><CENTER>
            SIIP V.4.0
                <strong>BETA</strong>
          </CENTER></TD>
        </TR>
        <TR>
          <TD colspan="2"><CENTER>
          </CENTER></TD>
        </TR>
        <TR>
          <TD colspan="2"><CENTER>
            Copyright &copy; Plataforma Colombia ltda.
          </CENTER></TD>
        </TR>
        <TR>
          <TD colspan="2"><CENTER>
            designed by Edwin Ca&ntilde;on
          </CENTER></TD>
        </TR>
        <TR>
          <TD colspan="2"><hr></TD>
        </TR>
        
        <TR>
          <TD width="60" rowspan="4"><img src="fotos/<?php echo $_SESSION[CIUDADSIIP] ?>/personal/<?php echo $_SESSION[IDFUNCIONARIO] ?>.jpg" width="55" height="75" border="1" style="border-color:<?php echo $_SESSION["ESTILOLINEAS"] ?>"></TD>
          <TD width="184"><center>
            Usuario Actual 
          </center></TD>
        </TR>
        <TR>
          <td class="FUENTE11NEGRILLA"><center>
              <p class="FUENTE11NEGRILLABL"><?php echo $_SESSION[NOMBREFUNCIONARIO] ?> </p>
          </center></td>
          </TR>
        <TR>
          <td class="FUENTE11"><center>
              <p class="FUENTE11BL"><?php echo $_SESSION[CARGOFUNCIONARIO]  ?>&nbsp;</p>
          </center></td>
          </TR>
        <TR>
          <TD>&nbsp;</TD>
        </TR>
        <TR>
          <TD colspan="2"><hr></TD>
        </TR>
        <TR>
          <TD colspan="2"> 
            <div align="left">Sucursal de Trabajo SIIP 
            &quot; <?php echo $_SESSION[NOMBRESUCURSAL] ?>&quot;            </div></TD>
          </TR>
        <TR>
          <TD colspan="2"><div align="left">Sucursal de Trabajo SAI-OPEN 
            &quot; <?php echo $_SESSION[SAIOPEN]  ?> &quot;
          </div></TD>
          </TR>
        <TR>
          <TD colspan="2"><hr></TD>
        </TR>
      </table>      </td>
    </tr>
  </table>
