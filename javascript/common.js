/*
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.net

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------
 * $Id: common.js,v 1.10.2.2 2007-10-21 22:39:47 giallu Exp $
 * --------------------------------------------------------
 */

/*
 * String manipulation
 */

function Trim( p_string ) {
	if (typeof p_string != "string") {
		return p_string;
	}

	var t_string = p_string;
	var t_ch = '';

	// Trim beginning spaces

	t_ch = t_string.substring( 0, 1 );
	while ( t_ch == " " ) {
		t_string = t_string.substring( 1, t_string.length );
		t_ch = t_string.substring( 0, 1 );
	}

	// Trim trailing spaces

	t_ch = t_string.substring( t_string.length-1, t_string.length );
	while ( t_ch == " " ) {
		t_string = t_string.substring( 0, t_string.length-1 );
		t_ch = t_string.substring( t_string.length-1, t_string.length );
	}

	return t_string;
}


/*
 * Cookie functions
 */

function GetCookie( p_cookie ) {
	var t_cookie_name = "MANTIS_" + p_cookie;
	var t_cookies = document.cookie;

	t_cookies = t_cookies.split( ";" );

	var i = 0;
	while( i < t_cookies.length ) {
		var t_cookie = t_cookies[ i ];

		t_cookie = t_cookie.split( "=" );

		if ( Trim( t_cookie[ 0 ] ) == t_cookie_name ) {
			return( t_cookie[ 1 ] );
		}
		i++;
	}

	return -1;
}

function SetCookie( p_cookie, p_value ) {
	var t_cookie_name = "MANTIS_" + p_cookie;
	var t_expires = new Date();

	t_expires.setTime( t_expires.getTime() + (365 * 24 * 60 * 60 * 1000));

	document.cookie = t_cookie_name + "=" + p_value + "; expires=" + t_expires.toUTCString() + ";";
}


/*
 * Collapsible element functions
 */

var g_div_history       = 0x0001;
var g_div_bugnotes      = 0x0002;
var g_div_bugnote_add   = 0x0004;
var g_div_bugnotestats  = 0x0008;
var g_div_upload_form   = 0x0010;
var g_div_monitoring    = 0x0020;
var g_div_sponsorship   = 0x0040;
var g_div_relationships = 0x0080;
var g_div_filter        = 0x0100;


/* List here the sections open by default */
var g_default_view_settings = 
	g_div_history | 
	g_div_bugnotes |
	g_div_bugnote_add |
	g_div_bugnotestats |
	g_div_upload_form |
	g_div_monitoring |
	g_div_sponsorship |
	g_div_relationships;


function GetViewSettings() {
	var t_cookie = GetCookie( "VIEW_SETTINGS" );

	if ( -1 == t_cookie ) {
		t_cookie = g_default_view_settings;
	} else {
		t_cookie = parseInt( t_cookie );
	}

	return t_cookie;
}

function SetDiv( p_div, p_cookie_bit ) {
	var t_view_settings = GetViewSettings();

	if( t_view_settings & p_cookie_bit ) {
		document.getElementById( p_div + "_open" ).style.display = "";
		document.getElementById( p_div + "_closed" ).style.display = "none";
	} else {
		document.getElementById( p_div + "_open" ).style.display = "none";
		document.getElementById( p_div + "_closed" ).style.display = "";
	}
}

function ToggleDiv( p_div, p_cookie_bit ) {
	var t_view_settings = GetViewSettings();

	t_view_settings ^= p_cookie_bit;
	SetCookie( "VIEW_SETTINGS", t_view_settings );

	SetDiv( p_div, p_cookie_bit );
}

/* Check checkboxes */
function checkall( p_formname, p_state) {
	var t_elements = (eval("document." + p_formname + ".elements"));

	for (var i = 0; i < t_elements.length; i++) {
    	if(t_elements[i].type == 'checkbox') {
      		t_elements[i].checked = p_state;
   		}
  	}
}

// global code to determine how to set visibility
var a = navigator.userAgent.indexOf("MSIE");
var style_display;

if (a!= -1) {
  style_display = 'block';
} else {
  style_display = 'table-row';
}
style_display = 'block';

function setDisplay(idTag, state) 
{
  if(!document.getElementById(idTag)) alert('SetDisplay(): id '+idTag+' is empty');
  // change display visibility
  if ( state != 0 ) {
      document.getElementById(idTag).style.display = style_display;
  } else {
      document.getElementById(idTag).style.display = 'none';
  }
}

function toggleDisplay(idTag) 
{
  setDisplay( idTag, (document.getElementById(idTag).style.display == 'none')?1:0 );
}

/* Append a tag name to the tag input box, with repect for tag separators, etc */
function tag_string_append( p_string ) {
	t_tag_separator = document.getElementById('tag_separator').value;
	t_tag_string = document.getElementById('tag_string');
	t_tag_select = document.getElementById('tag_select');
	if ( t_tag_string.value != '' ) {
		t_tag_string.value = t_tag_string.value + t_tag_separator + p_string;
	} else {
		t_tag_string.value = t_tag_string.value + p_string;
	}
	t_tag_select.selectedIndex=0;
}

/* funções customizadas do sistema de tarefas PROSEGUR 04/03/2011 */
function txtfield() {
	var txt = document.getElementById('txtnum');
	if ((txt.size <= 33) & (txt.value.length > 4)) {
		txt.size = txt.value.length + 1;
	} else if ((txt.value.length < 33 ) & (txt.value.length > 4)) {
		txt.size = txt.value.length;
	}
	
}

function soNumero()
{
	var iKey = (window.event)?event.keyCode:e.which;
	 if (!((iKey > 47 && iKey < 58) || iKey == 0 || iKey == 8 || iKey == 13)) {
		return false;
	 }
		
}

function soNumeroVirg()
{
	var iKey = (window.event)?event.keyCode:e.which;
	 if (!((iKey > 47 && iKey < 58) || iKey == 0 || iKey == 8 || iKey == 13  || iKey == 59)) {
		return false;
	 }
}

//função que valida os campos de preenchimento obrigatório no envio da tarefa
function validaCampos()
{
	var campos = '';
	 if (document.getElementById('data_inicio').value == '') {
		campos = 'Data de Início, ';
	 }
	if (document.getElementById('data_fim').value == '') {
		campos = campos + 'Data de Término, ';
	}
	if (document.getElementById('summary').value == '') {
		campos = campos + 'Título, ';
	}
	if (document.getElementById('description').value == '') {
		campos = campos + 'Descrição, ';
	}
	
	if (!campos == '') {
		alert('O(s) Campo(s): '+ campos + ' não foi(ram) preenchido(s).');
		return false;
	}
}

//função que valida os campos de preenchimento obrigatório no envio da tarefa integrada
function validaCamposIntegrated1() {
    if ( document.getElementById('casos[]').selectedIndex == -1 ) {
        alert("Favor selecionar pelo menos um caso para o cadastro.");
        return false;
    }
}

//variavel global usada para receber o campo da tarefa requisitado no banco
var task_field;

function updateCoverage(id, input) {
    if (valida100(document.getElementById(input).value)) {
        loadStartStop(1);
        obj = 'changeCoverage' + id;
        showObj(obj);
        obj = 'coverageValue' + id;
        old_val = document.getElementById(obj).innerHTML;  //valor antigo
        document.getElementById(obj).innerHTML = document.getElementById(input).value;
        changeTaskCoverage(obj, id, document.getElementById(input).value);
        //verifica se a cobertura está sendo alterada para 100%, se for, chama a função que altera a tarefa para entregue/finalizada
        if (document.getElementById(input).value == 100) {
            close_task(id);
            //verifica se foi atualizado usando a página de ver tarefas ou dentro de uma tarefa específica, 
            //se for, atualiza a página para mostrar o novo status da tarefa, que é fechado, 
            //se estiver atualizando dentro da pagina de relatório parcial, 
            //não faz o refresh porque por causar perda de dados do usuário que já preencheu outras coisas na tela
            if (this.document.URL.indexOf("view", 0) > 0) {
                location.reload(true);
            }
        } else if (old_val == 0) { //se está saindo de 0%, faz reload 
            if (this.document.URL.indexOf("view", 0) > 0) {
                window.setTimeout('location.reload(true)', 3000); //da refresh na página depois de 3 segundos
            }
        }
        loadStartStop(0);
    }
}

function updateHours(id, input) {
    loadStartStop(1);
    obj = 'changeHours' + id;
    showObj(obj);
    obj = 'hoursValue' + id;
    document.getElementById(obj).innerHTML = document.getElementById(input).value;
    changeTaskHours(obj, id, document.getElementById(input).value);
    loadStartStop(0);
}

//insere uma observação no relatório parcial
function updateNote(id, input) {
    loadStartStop(1);
    text = document.getElementById(input).value;
    obj = 'changeNote' + id;
    showObj(obj);
    obj = 'NoteValue' + id;
    if (text.length > 10) { //envia os ... (tres pontos) apenas se houver texto maior que 10 caracteres
        document.getElementById(obj).innerHTML = text.substring(0, 10) + "..."; //envia parte do texto para o label
    } else {
    document.getElementById(obj).innerHTML = text; //envia o texto para o label
    }
    document.getElementById(obj).title = text; //envia o texto para o title, mostra ao passar o mouse
    loadStartStop(0);
}

//mostra ou oculta um objeto pelo id, se estiver mostrando ele oculta, se estiver oculto ele exibe PROSEGUR 01/06/2011
function showObj(id) {
    var obj = document.getElementById(id);
    if (obj.style.display == 'block') {
        obj.style.display = 'none';
    } else if ((obj.style.display == 'none') || (obj.style.display == '')) {
    obj.style.display = 'block';
    }

}

function valida100(valor) {
    if (valor > 100) {
        alert("O valor máximo permitido é 100%");
        return false;
    } else if (valor < 0) {
        alert("O valor mínimo permitido é 0%");
        return false;
    } else  {
    return true;
    }
}

//função que busca no banco informação sobre uma tarefa, recebe o id e o campo que se quer a informação na tabela
function getTaskField(id, field) {
    loadStartStop(1);
    new Ajax.Request('task_util.php',

                  {
                      asynchronous: false,
                      method: 'POST',
                      parameters: 'action=getTaskField&taskid=' + id + '&field=' + field,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              task_field = resp.responseText;
                          }
                      }
                  }
            );
    loadStartStop(0);
}

function changeTaskCoverage(obj, id, value) {
    loadStartStop(1);
    new Ajax.Request('task_util.php',

                  {
                      asynchronous: true,
                      method: 'POST',
                      parameters: 'action=changeCove&taskid=' + id + '&value=' + value,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              //document.getElementById(obj).innerHTML = resp.responseText;
                          }
                      }
                  }
            );
                  loadStartStop(0); 
              }

              function changeTaskHours(obj, id, value) {
                  new Ajax.Request('task_util.php',

                  {
                      asynchronous: true,
                      method: 'POST',
                      parameters: 'action=changeHours&taskid=' + id + '&value=' + value,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              //document.getElementById(obj).innerHTML = resp.responseText;
                          }
                      }
                  }
            );
              }

              function getInner(id) {
                  return document.getElementById(id).innerHTML;
              }

              //inicia e para o loading de acordo com a ação, 1 = inicia, 0 = parar
              function loadStartStop(acao) {
                  if (acao == 0) {
                      showObj('carregando');
                      showObj('txtLoading');// document.getElementById('txtLoading').style.display = 'none';
                      showObj('imgLoading');// document.getElementById('imgLoading').style.display = 'none';
                  } else if (acao == 1) {
                  showObj('carregando'); //document.getElementById('carregando').style.display = 'block';
                  showObj('txtLoading'); //document.getElementById('txtLoading').style.display = 'block';
                  showObj('imgLoading'); //document.getElementById('imgLoading').style.display = 'block';
                  }
              }


 function avatarChange(user) {
     new Ajax.Request('task_util.php',

                  {
                      asynchronous: false,
                      method: 'POST',
                      parameters: 'action=change_avatar&file=' + document.getElementById('avatar').value + '&user=' + user,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              document.getElementById("avatarImg").src = resp.responseText;
                          }
                      }
                  }
            );

              }
 
//função que apaga o avatar temporário, ao sair da página o sistema verifica se existe um ou mais avatar(es) temporário(s) para o usuário, se existir, apaga
              function onunloadDellAvatar(user) {
                  new Ajax.Request('task_util.php',

                  {
                      asynchronous: false,
                      method: 'POST',
                      parameters: 'action=dellAvatarTemp',
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              
                          }
                      }
                  }
            );
              }
                
              //verifica se o campo do arquivo está preenchido e exibe uma msg personalizada, se for o botão alterar, o file pode não estar preenchido, mas 
              function fieldBlank(field, msg) {
                  if (document.getElementById(field).value == '') {
                      alert(msg);
                      return false;
                  }
              }

              function send_parcial_reporter(form) {
                  var count = 0;
                  var array_tasks, array_notes, array_bugs;
                //verifica se o grid possui registros, existe um "problema" que quando tem apenas 1 registro o bug_arr[] é envido de forma diferente, tendo que ser tratado diferente
                  if (form["bug_arr[]"].length != undefined) {
                      for (i = 0; i < form["bug_arr[]"].length; i++) {
                          //pega os marcados
                          if ((form["bug_arr[]"][i].checked) | (form["bug_arr[]"].textLength > 0)) {
                              id = form["bug_arr[]"][i].value;
                              array_tasks += "|" + id;
                              //pega as notas
                              noteValueObs = "noteValueObs" + id;
                              //pega os bugs associados
                              bugsTasks = "bugs" + id;
                              if (navigator.appName.indexOf('Internet Explorer') > 0) { //se for IE posso usar o innerHTML, no FF não funcionou
                                  array_notes += "|" + form[noteValueObs].innerHTML;
                              } else {  //demais browsers
                                  array_notes += "|" + form[noteValueObs].value;
                              }
                              array_bugs += "|" + form[bugsTasks].value;
                              count++;
                          }
                      } //fim do for
                  } //fim do if de verificação de registros
                  else if (form["bug_arr[]"] != undefined) { //verifica se tem apenas 1 registro na tela, se não for marcado nenhum o array é undefined
                      //antes de tudo, verifica se esse único registro foi marcado CHECKED, caso contrário não faz nada
                  //verifica se esse 1 registro está marcado
                  if (form.elements[0].checked) {
                      //pegar o id do unico registro na tela
                          var id = '';
                          id = form.elements[0].value;
                          //verifica se existe um id
                          if (id > 0) {
                              array_tasks += "|" + id;
                              //pega as notas
                              noteValueObs = "noteValueObs" + id;
                              //pega os bugs associados
                              bugsTasks = "bugs" + id;
                              if (navigator.appName.indexOf('Internet Explorer') > 0) { //se for IE posso usar o innerHTML, no FF não funcionou
                                  array_notes += "|" + form[noteValueObs].innerHTML;
                              } else {  //demais browsers
                                array_notes += "|" + form[noteValueObs].value;
                               }
                                array_bugs += "|" + form[bugsTasks].value;

                              count++;
                          }
                      } //fim da pesquisa do checked, existe apenas 1 registro na tela, mas ele não foi marcado
                  } //fim da verificação se existe apenas 1 na tela

                  //não foi selecionado nada
                  if (count == 0) {
                      alert("Não foi selecionado nenhum registro!");
                      return false;
                  }
                  //usado para fazer funcionar quando é inserido caracteres especiais e o sinal de % (porcentagem) na anotação, estava dando problema de codificação da URI
                  array_notes = encodeURIComponent(array_notes);
                  new Ajax.Request('task_util.php',

                  {
                      asynchronous: true,
                      method: 'POST',
                      parameters: 'action=email_partial_report&array_tasks=' + array_tasks + '&array_notes=' + array_notes + '&array_bugs=' + array_bugs,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              alert(resp.responseText);
                          }
                      }
                  }
            );
            


              }

                //função utilitária para debug, exibe um alert com o conteúdo de um objeto array java script, estilo a print_r do php
              function print_r(obj) {
                  var output = '';
                  for (property in obj) {
                      output += property + ': ' + obj + '; ';
                  }
                  alert(output);
              }

              function close_task(id_task) {
                  
                  if (confirm("Deseja finalizar a tarefa?")) {
                    //exibe campo para anotação
                      tasknote_text = prompt("Inserir uma anotação:", "");
                  
                  new Ajax.Request('task_util.php',

                  {
                      asynchronous: false,
                      method: 'POST',
                      parameters: 'action=bug_close&id=' + id_task + '&tasknote_text=' + tasknote_text,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {

                          }
                      }
                  }
            );
            } //fim do id de decisão de fechar ou não

        }

        //função que limpa as datas do filtro de relatório parcial
        function clear_date() {
            document.getElementById('date_start').value = '';
            document.getElementById('date_end').value = '';
        }
        
        //exibe mais campos de data de acordo com a quantidade de registros selecionados na abertura da tarefa integrada
        function show_date_number(select) {
            //verifica os selecionados
            var selectedArray = new Array();
            var selObj = select;
            var i, k;
            var count = 0;

            //chamar a função que cria dinamicamente os campos de data de acordo com a quantidade de registros retornados

            //esconde o campo de sumario de bugs
            document.getElementById("type_summary").style.display = 'none';

            //esconde todos os campos de data
            for (k = 1; k <= selObj.options.length; k++) {
                if (document.getElementById(k) != null) {
                    document.getElementById(k).style.display = 'none';
                }
            }
            if (document.getElementById("50_1") != null) {
                document.getElementById("50_1").style.display = 'none';
            }
            
            //verifica se é bugs ou demandas, se for demandas sao n campos de datas para mostrar/esconder se for bugs é apenas 1
            if (document.getElementById("type_task10").checked == true) {

                    for (i = 0; i < selObj.options.length; i++) {
                        if (selObj.options[i].selected) {
                            selectedArray[count] = selObj.options[i].value;
                            if (navigator.appName.indexOf('Internet Explorer') > 0) { //se for IE posso usar o none, se for ff usar table-row
                                document.getElementById(count + 1).style.display = 'block';
                            }
                            else {
                                document.getElementById(count + 1).style.display = 'table-row';
                            }
                            document.getElementById('label' + (count + 1)).innerHTML = '(' + selObj.options[i].value + ')';
                            count++;
                        }

                    } //fim do for
            } //fim do if do demandas
            //verifica se é bugs que está marcado
            else if (document.getElementById("type_task50").checked == true) {
            //verifica se tem alguém selecionado para exibir ou ocultar o campo de data
            for (i = 0; i < selObj.options.length; i++) {
                if (selObj.options[i].selected) {

                    if (navigator.appName.indexOf('Internet Explorer') > 0) { //se for IE posso usar o none, se for ff usar table-row
                        document.getElementById("50_1").style.display = 'block';
                        document.getElementById("type_summary").style.display = 'block';
                    }
                    else {
                        document.getElementById("50_1").style.display = 'table-row';
                        document.getElementById("type_summary").style.display = 'table-row';
                    }
                }
            }
                    }

                }

                function dinamicStatusFilter(current_proj, inst) {
                    
                    //verifica os relatores selecionados no filtro
                    var a = document.getElementsByName('handlerFilter[]');
                    var users = '00'; //00 porque dou um substring nela depois removendo o ultimo caracter por causa da virgula
                    if (a[0].selectedIndex > -1) { //verifica se foi selecionado alguém, se não foi vai fazer a busca por todos, inclusive os usuários desabilitados
                        for (w = 0; w < a[0].length; w++) {
                            if (a[0][w].selected == true) {
                                //alert(a[0][w].value);
                                users += a[0][w].value + ",";
                            }
                        }
                    }
                    var sel = 0;
                    //verifica se é demanda ou bug para pegar o grupo de checkbox correto
                    for (k = 0; k < inst.length; k++) {
                        if (inst[k].checked == true) {
                            var id_sys = inst[k].id;
                            var objType = inst[k].value;
                        }
                    }
                    //compara o id que foi selecionado no radio e seleciona o id correspondende do grupo de check de status
                    if (id_sys == "type_task10") {  //type_task10 = demandas e type_task50 = bugs
                        var objCheckBox = document.forms["report_bug_form"].elements['deman_status'];
                    } else {
                        var objCheckBox = document.forms["report_bug_form"].elements['bug_status'];
                    }
                    var selecionadas = "";

                    for (i = 0; i < objCheckBox.length; i++) {
                        if (objCheckBox[i].checked) {
                            sel++;
                            selecionadas += objCheckBox[i].value + ",";
                        }
                    }
                    if (sel > 0) {
                        selectTypeTask(objType, current_proj, selecionadas.substring(0, selecionadas.length - 1), users.substring(0, users.length - 1));
                    }
                    else {
                    
                    }
                }

             function selectTypeTask(obj, current_proj, status, user) { //atributo user é para dizer se vai buscar os usuários novamente ou não, útil para usar a função em outros lugares
                 //exibe a lista de status para o filtro dependendo do tipo do sistema
                 if (obj == 10) {
                     document.getElementById("demanda_status").style.display = 'block';
                     document.getElementById("bug_status").style.display = 'none';
                     //desmarca todos os checks dos status de bugs e marca os padrões iniciais da tela
                     document.forms["report_bug_form"].elements["bug_status"][0].checked = false;
                     document.forms["report_bug_form"].elements["bug_status"][1].checked = false;
                     document.forms["report_bug_form"].elements["bug_status"][2].checked = false;
                     document.forms["report_bug_form"].elements["bug_status"][3].checked = false;
                     document.forms["report_bug_form"].elements["bug_status"][4].checked = true;
                     document.forms["report_bug_form"].elements["bug_status"][5].checked = true;
                 }
                 else {
                     document.getElementById("demanda_status").style.display = 'none';
                     document.getElementById("bug_status").style.display = 'block';
                     //desmarca todos os checks dos status de demandas e marca os padrões iniciais da tela
                     document.forms["report_bug_form"].elements["deman_status"][0].checked = false;
                     document.forms["report_bug_form"].elements["deman_status"][1].checked = false;
                     document.forms["report_bug_form"].elements["deman_status"][2].checked = false;
                     document.forms["report_bug_form"].elements["deman_status"][3].checked = false;
                     document.forms["report_bug_form"].elements["deman_status"][4].checked = true;
                 }
            
            //remove a seleção do select antes de trocar a opção, evita que os campos de data sejam exibidos incorretamente
            document.getElementById('casos[]').selectedIndex = -1;
            //chama a função para esconder os campos de data, caso seja selecionado alguma opção
            show_date_number(document.getElementById('casos[]'));
            //verifica se é para filtrar novamente os usuários também
            if (user == 1) {
                show_users_proj(obj, current_proj, 1);
            } else { //se users <> 1 é porque foi enviado usuários para filtrar
                var users = user;
            }
            new Ajax.Request('task_util.php',
                  {
                      asynchronous: true,
                      method: 'POST',
                      parameters: 'action=show_cases&sistem=' + obj + '&current_proj=' + current_proj + '&status=' + status + '&users=' + users,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              document.getElementById('caso').innerHTML = resp.responseText;
                          }
                      }
                  }
            );
              }
              
              function show_users_proj(sis, current_proj, show){
                  new Ajax.Request('task_util.php',
                  {
                      asynchronous: true,
                      method: 'POST',
                      parameters: 'action=show_users&sistem=' + sis + '&current_proj=' + current_proj + '&show=' + show,
                      onLoading: function(resp) {
                          document.getElementById(id).innerHTML = 'Aguarde processamento...';

                      },
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro ao efetuar consulta, tente novamente.');
                          }
                          else {
                              document.getElementById('handlerFilter').innerHTML = resp.responseText;
                          }
                      }
                  }
                );
              }

              function ldap(user, name, email) {  //faz pesquisa no ldap no cadastro de usuários para facilitar o preenchimento dos campos
                  if (document.getElementById(user).value == '') {
                      if (document.getElementById(name).value == '') {
                          if (document.getElementById(email).value == '') {
                              alert('Pelo menos um campo deve ser preenchido');
                              return;
                          } 
                      } 
                  }

                  new Ajax.Request('task_util.php',
                  {
                      asynchronous: true,
                      method: 'POST',
                      parameters: 'action=search_ldap&user=' + document.getElementById(user).value + '&name=' + document.getElementById(name).value,
                      onComplete: function(resp) {
                          if (resp.responseText == 'erro') {
                              alert('Erro 30! Contate o administrador.');
                          }
                          else {
                              //inicia o loading
                              document.getElementById('carregando').style.display = 'block';
                              var temp = resp.responseText.split('&'); //separa pelo script, ficando no temp[2] os dados separados por |
                              //verifica o retorno através do número de controle, 1 = registro encontrado, 2 = mais de um retorno e 3 = registro não encotnrado
                              if (temp[0] == '3') {
                                  alert("Usuário não encontrado, tente refinar a consulta!");
                              } else if (temp[0] == '2') {
                                  alert("Mais de um usuário encontrado para o filtro informado, verifique: \n" + temp[1]);
                              } else {  //usuário encontrado, trata os dados e preenche os campos na tela
                                  temp = temp[1].split("|");
                                  document.getElementById(user).value = temp[1];
                                  document.getElementById(name).value = temp[0];
                                  document.getElementById(email).innerHTML = temp[2];
                              }
                              //interrompe o loading
                              document.getElementById('carregando').style.display = 'none';
                          }
                      }
                  }
            );
              }


