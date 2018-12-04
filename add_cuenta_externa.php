<?php
  $page_title = 'Alta de Cuenta por Pagar';
  require_once('includes/load.php');
  
  // Checkin What level user has permission to view this page
  page_require_level("tesoreria");
  $e_user = find_by_id('users', (int)$_SESSION['user_id']);
  
  if(!$e_user){
    $session->msg("d","No existe el ID de Usuario.");
    redirect('index.php');
  }
  
  if(isset($_POST['add_cuenta_externa'])){
    $req_fields = array('proveedor','descripcion','fecha_pago','monto_pago', 'peridiocidad');
    validate_fields($req_fields);
        
    if(empty($errors)){
      // Obtenemos último ID de requisición
      $proveedor = $db->escape($_POST['proveedor']);
      $descripcion = remove_junk($db->escape($_POST['descripcion']));
      $fecha_pago = $db->escape($_POST['fecha_pago']);
      $monto_pago = $db->escape($_POST['monto_pago']);
      $peridiocidad = (int)$db->escape($_POST['peridiocidad']);
      $insertCuentaExterna = "INSERT INTO tesoreria(id,tipo_cuenta,proveedor,nombre,descripcion,fecha_pago,abono,monto_pago,peridiocidad,tipo,comprobante) VALUES (NULL,'1','$proveedor','','Cuenta Externa','$fecha_pago',0.00,0.00,'$peridiocidad',0,'');";
      
      if($db->query($insertCuentaExterna)){
        $session->msg('s',"Cuenta Ingresada correctamente");
        redirect('tesoreria_externas.php', false);
      } else {
        $session->msg('d','Lo sentimos, ocurrió un error creado la cuenta por pagar');
        redirect('tesoreria_externas.php', false);
      }
    }
  }
  
  include_once('layouts/header.php');
?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Alta de Cuenta por Pagar</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_cuenta_externa.php" autocomplete="off">
          <div class="col-md-12">
            <div class="form-group">
              <label for="proveedor">Nombre, Empresa u Organización de Pago</label>
              <input type="text" class="form-control" name="proveedor" id="proveedor" value="">
            </div>
            <div class="form-group">
              <label for="descripcion">Descripción</label>
              <textarea class="form-control" type="text" name="descripcion" id="descripcion" value=""></textarea>
            </div>
            <div class="form-group">
              <label for="fecha_pago">Fecha de Pago</label>
              <input type="date" class="form-control" name="fecha_pago" id="fecha_pago" value="">
            </div>
            <div class="form-group">
              <label for="monto_pago">Monto de Pago</label>
              <input type="text" class="form-control numberOnly" name="monto_pago" id="monto_pago" autocomplete="off" maxlength="6">
            </div>
            <div class="form-group">
              <label for="peridiocidad">Peridiocidad</label>
              <select class="form-control" name="peridiocidad">
                <option value="">-- Seleccionar el Rango --</option>
                <option value="1">7 días</option>
                <option value="2">15 días</option>
                <option value="3">30 días</option>
                <option value="4">45 días</option>
              </select>
            </div>
            <div class="form-group clearfix" align="right">
              <input type="hidden" id="LiqSelection" name="LiqSelection"/>
              <button type="submit" id="add_cuenta_externa" name="add_cuenta_externa" class="btn btn-primary">Crear</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_cuenta_externa').click(function() {
        return window.confirm("¿Desea crear una Cuenta Externa?");
    });
});

jQuery('.numberOnly').on('keydown', function(e) {
	
  if (this.selectionStart || this.selectionStart == 0) {
	// selectionStart won't work in IE < 9
	
	var key = e.which;
	var prevDefault = true;
	
	var thouSep = ",";  // your seperator for thousands
	var deciSep = ".";  // your seperator for decimals
	var deciNumber = 2; // how many numbers after the comma
	
	var thouReg = new RegExp(thouSep,"g");
	var deciReg = new RegExp(deciSep,"g");
	
	function spaceCaretPos(val, cPos) {
		/// get the right caret position without the spaces
		
		if (cPos > 0 && val.substring((cPos-1),cPos) == thouSep)
		  cPos = cPos-1;
		
		if (val.substring(0,cPos).indexOf(thouSep) >= 0) {
		  cPos = cPos - val.substring(0,cPos).match(thouReg).length;
		}
		
		return cPos;
	}
	
	function spaceFormat(val, pos) {
		/// add spaces for thousands
		
		if (val.indexOf(deciSep) >= 0) {
			var comPos = val.indexOf(deciSep);
			var int = val.substring(0,comPos);
			var dec = val.substring(comPos);
		} else{
			var int = val;
			var dec = "";
		}
		var ret = [val, pos];
		
		if (int.length > 3) {
			
			var newInt = "";
			var spaceIndex = int.length;
			
			while (spaceIndex > 3) {
				spaceIndex = spaceIndex - 3;
				newInt = thouSep+int.substring(spaceIndex,spaceIndex+3)+newInt;
				if (pos > spaceIndex) pos++;
			}
			ret = [int.substring(0,spaceIndex) + newInt + dec, pos];
		}
		return ret;
	}
	
	jQuery(this).on('keyup', function(ev) {
		
		if (ev.which == 8) {
			// reformat the thousands after backspace keyup
			
			var value = this.value;
			var caretPos = this.selectionStart;
			
			caretPos = spaceCaretPos(value, caretPos);
			value = value.replace(thouReg, '');
			
			var newValues = spaceFormat(value, caretPos);
			this.value = newValues[0];
			this.selectionStart = newValues[1];
			this.selectionEnd   = newValues[1];
		}
	});
	
	if ((e.ctrlKey && (key == 65 || key == 67 || key == 86 || key == 88 || key == 89 || key == 90)) ||
	   (e.shiftKey && key == 9)) // You don't want to disable your shortcuts!
		prevDefault = false;
	
	if ((key < 37 || key > 40) && key != 8 && key != 9 && prevDefault) {
		e.preventDefault();
		
		if (!e.altKey && !e.shiftKey && !e.ctrlKey) {
		
			var value = this.value;
			if ((key > 95 && key < 106)||(key > 47 && key < 58) ||
			  (deciNumber > 0 && (key == 110 || key == 188 || key == 190))) {
				
				var keys = { // reformat the keyCode
          48: 0, 49: 1, 50: 2, 51: 3,  52: 4,  53: 5,  54: 6,  55: 7,  56: 8,  57: 9,
          96: 0, 97: 1, 98: 2, 99: 3, 100: 4, 101: 5, 102: 6, 103: 7, 104: 8, 105: 9,
          110: deciSep, 188: deciSep, 190: deciSep
				};
				
				var caretPos = this.selectionStart;
				var caretEnd = this.selectionEnd;
				
				if (caretPos != caretEnd) // remove selected text
				value = value.substring(0,caretPos) + value.substring(caretEnd);
				
				caretPos = spaceCaretPos(value, caretPos);
				
				value = value.replace(thouReg, '');
				
				var before = value.substring(0,caretPos);
				var after = value.substring(caretPos);
				var newPos = caretPos+1;
				
				if (keys[key] == deciSep && value.indexOf(deciSep) >= 0) {
					if (before.indexOf(deciSep) >= 0) newPos--;
					before = before.replace(deciReg, '');
					after = after.replace(deciReg, '');
				}
				var newValue = before + keys[key] + after;
				
				if (newValue.substring(0,1) == deciSep) {
					newValue = "0"+newValue;
					newPos++;
				}
				
				while (newValue.length > 1 && newValue.substring(0,1) == "0" && newValue.substring(1,2) != deciSep) {
					newValue = newValue.substring(1);
					newPos--;
				}
				
				if (newValue.indexOf(deciSep) >= 0) {
					var newLength = newValue.indexOf(deciSep)+deciNumber+1;
					if (newValue.length > newLength) {
					  newValue = newValue.substring(0,newLength);
					}
				}
				
				newValues = spaceFormat(newValue, newPos);
				
				this.value = newValues[0];
				this.selectionStart = newValues[1];
				this.selectionEnd   = newValues[1];
			}
		}
	}
	
	jQuery(this).on('blur', function(e) {
		
		if (deciNumber > 0) {
			var value = this.value;
			
			var noDec = "";
			for (var i = 0; i < deciNumber; i++) noDec += "0";
			
			if (value == "0" + deciSep + noDec) {
        this.value = ""; //<-- put your default value here
      } else if(value.length > 0) {
				if (value.indexOf(deciSep) >= 0) {
					var newLength = value.indexOf(deciSep) + deciNumber + 1;
					if (value.length < newLength) {
					  while (value.length < newLength) value = value + "0";
					  this.value = value.substring(0,newLength);
					}
				}
				else this.value = value + deciSep + noDec;
			}
		}
	});
  }
});

jQuery('.price > input:eq(0)').focus();
</script>
<?php include_once('layouts/footer.php'); ?>