// @todo: mettre une barre loading et readonly qd save
if(typeof dev == 'undefined') dev = false;

// Traduction
add_translation({
	"Save" : {"fr" : "Enregistrer"},
	"Archive" : {"fr" : "Archiver"},
	"Archive the page" : {"fr" : "Archiver la page"},
	"Delete" : {"fr" : "Supprimer"},
	"Delete the page" : {"fr" : "Supprimer la page"},
	"Warning, this will permanently delete the page" : {"fr" : "Attention, ceci supprimera définitivement la page"},
	"Also remove media from content" : {"fr" : "Supprimer également les médias présents dans le contenu"},
	"The changes are not saved" : {"fr" : "Les modifications ne sont pas enregistrées"},
	"Cancel" : {"fr" : "Annuler"},	

	"Empty element" : {"fr" : "Elément vide"},		
	"Edit menu" : {"fr" : "Editer le menu"},		
	"To remove slide here" : {"fr" : "Pour supprimer glisser ici"},		
	"Paste something..." : {"fr" : "Collez quelque chose..."},
	"Upload file" : {"fr" : "Télécharger un fichier"},
	"Change the background image" : {"fr" : "Changer l'image de fond"},
	"This file format is not supported" : {"fr" : "Ce format de fichier n'est pas pris en charge"},	
	"Delete user" : {"fr" : "Supprimer l'utilisateur"},
	"Title" : {"fr" : "Titre"},	
	"Media Library" : {"fr" : "Bibliothèque des médias"},		
	"Icon Library" : {"fr" : "Bibliothèque d'icône"},		
	"See the source code" : {"fr" : "Voir le code source"},		
	"Paragraph" : {"fr" : "Paragraphe"},		
	"Quote line" : {"fr" : "Ligne de citation"},		
	"Blockquote" : {"fr" : "Bloc de citation"},
	"Highlight" : {"fr" : "Mise en avant"},		
	"Grid" : {"fr" : "Grille"},		
	"Column" : {"fr" : "Colonne"},		
	"Bold" : {"fr" : "Gras"},		
	"Italic" : {"fr" : "Italique"},		
	"Underline" : {"fr" : "Souligner"},		
	"Superscript" : {"fr" : "Exposant"},	
	"Insert list" : {"fr" : "Insérer une liste"},	
	"Justify Left" : {"fr" : "Justifier à gauche"},	
	"Justify Center" : {"fr" : "Justifier au centre"},	
	"Justify Right" : {"fr" : "Justifier à droite"},	
	"Justify" : {"fr" : "Justifier"},	
	"Language" : {"fr" : "Langue"},		
	"Add Language" : {"fr" : "Ajouter une langue"},		
	"Change Language" : {"fr" : "Change la langue"},		
	"Video" : {"fr" : "Vidéo"},		
	"Show video" : {"fr" : "Afficher la vidéo"},	
	"Show video in new window" : {"fr" : "Afficher la vidéo dans une nouvelle fenêtre"},	
	"Add Video" : {"fr" : "Ajouter une vidéo"},		
	"Add Color" : {"fr" : "Ajouter une couleur au texte"},		
	"Anchor" : {"fr" : "Ancre"},		
	"Add Anchor" : {"fr" : "Ajouter une ancre"},		
	"Change Anchor" : {"fr" : "Modifier l'ancre"},		
	"Link" : {"fr" : "Lien"},		
	"Link text" : {"fr" : "Texte du lien"},		
	"Add Link" : {"fr" : "Ajouter le lien"},		
	"Change Link" : {"fr" : "Modifier le lien"},		
	"Open link in new window" : {"fr" : "Ouvre le lien dans une nouvelle fenêtre"},		
	"Destination URL" : {"fr" : "URL de destination"},	

	"Remove the link from the selection" : {"fr" : "Supprimer le lien de la sélection"},
	"Alt text" : {"fr" : "Texte alternatif"},		
	"Delete image" : {"fr" : "Supprimer l'image"},		
	"Delete file" : {"fr" : "Supprimer le fichier"},
	"Erase" : {"fr" : "Effacer"},
	"Image dimension in pixel (width x height)" : {"fr" : "Dimension de l'image en pixel (largeur x hauteur)"},

	"Zoom link" : {"fr" : "Lien zoom"},
	"Add" : {"fr" : "Ajouter"},
	"Width" : {"fr" : "Largeur"},
	"Height" : {"fr" : "Hauteur"},
	"Optimize" : {"fr" : "Optimiser"},
	"Caption" : {"fr" : "Légende"},

	"Add a module" : {"fr" : "Ajouter un module"},
	"Move" : {"fr" : "Déplacer"},
	"Remove" : {"fr" : "Supprimer"},

	"Image optimization" : {"fr" : "Optimisation des images"},
	"Resize" : {"fr" : "Redimensionner"},
	"Convert to" : {"fr" : "Convertir en"},
	"Compress" : {"fr" : "Compresser"},
	"Limit" : {"fr" : "Limite"},
	"Background" : {"fr" : "Fond"},
});


// Type de navigateur
$.browser = {};
$.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
$.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
$.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
$.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());


// Rapatrie le contenu
get_content = function(content)
{
	// Supprime les index devant les class et id
	var content_array = content.replace(/\.|#/, '');

	data[content_array] = {};

	// Contenu des champs éditables
	$(document).find(content+" .editable:not(.global)").not("#main-navigation .editable").each(function() 
	{
		// Nettoie les <p> vides ou avec juste un <br> // Accessibilité
		/*$("p", this).each(function() {
			if($(this).html() == "<br>" || $(this).html() == "")
				$(this).replaceWith($('<div class="pbp"></div>'));			
		});*/

		// Duplique les figcaption dans un aria-label dans la figure // Accessibilité
		$("figcaption", this).each(function() {
			$(this).closest("figure").attr("aria-label", $(this).text());
		});

		// Si on est en mode pour voir le code source
		if($(this).hasClass("view-source")) var content_editable = $(this).text();
		else var content_editable = $(this).html();

		if($(this).html()) data[content_array][this.id] = content_editable;
	});
	
	// Contenu des images/vidéos éditables
	$(document).find(content+" .editable-media:not(.global) img, "+content+" .editable-media:not(.global) video").each(function() {
		if($(this).attr("src")) data[content_array][$(this).closest(".editable-media").attr("id")] = $(this).attr("src");
	});

	// Contenu des fichiers éditables
	$(document).find(content+" .editable-media .fa").each(function() {
		if($(this).attr("title")) data[content_array][$(this).closest("span").attr("id")] = $(this).attr("title");
	});
	
	// Contenu des background images éditables
	$(document).find(content+" [data-id][data-bg]:not([data-global]), "+content+"[data-id][data-bg]").each(function() {
		if($(this).attr("data-bg")) data[content_array][$(this).attr("data-id")] = $(this).attr("data-bg");
	});
		
	// Checkbox fa
	$(document).find(content+" .editable-checkbox").each(function() {
		if($(this).hasClass("fa-ok")) data[content_array][this.id] = true;					
	});

	// Contenu des select, input hidden, href éditables // content+" input, "+
	$(document).find(content+" .editable-select, "+content+" .editable-input, "+content+" .editable-href, "+content+" .editable-alt").each(function() {
		if($(this).attr("type") == "checkbox") data[content_array][this.id] = $(this).prop("checked");			
		else if($(this).attr("type") == "radio" && $(this).prop("checked")) data[content_array][this.name] = this.id;
		else if($(this).val()) data[content_array][this.id] = $(this).val(); 
	});
}


// Sauvegarde les contenus
save = function() //callback
{
	// @todo: disable/unbind sur save pour dire que l'on est en train de sauvegarder
	
	// Fonction à exécuter avant la collect des datas
	$(before_data).each(function(key, funct){ funct(); });

	// Si image sélectionnée : raz propriétés image (sécurité pour ne pas enregistrer de ui-wrapper)
	if(memo_img) img_leave();

	// Animation sauvegarde en cours (loading)
	$("#save i").removeClass("fa-floppy").addClass("fa-spin fa-cog");

	data = {};
	
	data["nonce"] = $("#nonce").val();// Pour la signature du formulaire

	data["id"] = id;// id de la page courante

	data["url"] = clean_url();// Url de la page en cours d'édition

	data["permalink"] = $("#admin-bar #permalink").val();// Permalink

	data["title"] = $("#admin-bar #title").val();// Titre de la page
	data["description"] = $("#admin-bar #description").val();// Description pour les serp

	data["state"] = ($("#admin-bar #state-content").prop("checked") == true ? "active" : "deactivate");// Etat d'activation de la page

	data["type"] = $("#admin-bar #type").val();// Type de contenu

	data["tpl"] = $("#admin-bar #tpl").val();// Template

	// Robots 
	if($("#admin-bar #noindex").prop("checked")) data["robots"] = "noindex";
	if($("#admin-bar #nofollow").prop("checked")) 
		data["robots"] = 
			(data["robots"]?data["robots"]:"") +
			(data["robots"]?", ":"") +
			"nofollow";

	data["date-insert"] = $("#admin-bar #date-insert").val();// Date de création de la page
	

	get_content(".content");// Contenu de la page

	get_content("header");// Contenu du header

	get_content("footer");// Contenu du footer


	// Donnée Méta
	data["meta"] = {};
	$(document).find(".content .editable-input.meta, .content .editable-select.meta").each(function() {
		data["meta"][this.id] = $(this).val();// if($(this).val()) 		
	});
	$(document).find(".content .editable.meta").each(function() {
		if($(this).html()) data["meta"][this.id] = $(this).html();					
	});

	// Donnée global commune à plusieur page
	data["global"] = {};
	// Texte
	$(document).find(".content .editable.global").each(function() {
		if($(this).html()) data["global"][this.id] = $(this).html();					
	});
	// Image
	$(document).find(".content .editable-media.global img").each(function() {
		if($(this).attr("src")) data["global"][$(this).closest(".editable-media").attr("id")] = $(this).attr("src");
	});
	// BG
	$(document).find(".content [data-id][data-bg][data-global]").each(function() {
		if($(this).attr("data-bg")) data["global"][$(this).attr("data-id")] = $(this).attr("data-bg");
	});


	// Tags de la fiche en cours
	data["tag"] = {};
	data["tag-separator"] = {};
	$(document).find(".content .editable-tag").each(function() 
	{
		// Séparateur de tag
		data["tag-separator"][$(this).attr("id")] = $(this).data("separator");

		// Tags
		if($(this).text()) data["tag"][$(this).attr("id")] = $(this).text();

		// Ordre forcé du tag
		if($(this).next(".editable-tag-ordre").val() != undefined) 
			data["tag-ordre"] = $(this).next(".editable-tag-ordre").val();
	});	

	// @SUPP Ajout des tags au contenu pour les recherches et affichage simplifié/rapide (ne pas affichager dans le contenu, car n'est pas remplacé massivement quand edit page d'un tag)
	//data["content"]["tag"] = JSON.stringify(data["tag"]);

	
	// Si sur page tag
	if(tag) 
	{
		// Ajoute les données prise dans le contenu
		data["tag-info"] = {};
		data["tag-info"]["title"] = data["tag"] = data["content"]["title"];
		data["tag-info"]["description"] = data["content"]["description"];
		data["tag-info"]["img"] = data["content"]["img"];
	}


	if($("#admin-bar #og-image img").attr("src"))
	data["content"]["og-image"] = $("#admin-bar #og-image img").attr("src");// Image pour les réseaux sociaux
	

	//@todo voir pourquoi ça ne supp pas de la nav quand on glisse sur poubelle un element du menu
	
	// Contenu du menu de navigation
	data["nav"] = {};
	//$(document).find("header nav ul li").not("#add-nav ul li, .exclude").each(function(i) {
	//$(document).find("header nav ul li a").not("#add-nav ul li a, .exclude").each(function(index, element) {
	// Ciblage plus précis pour moins de problèmes avec les autres nav dans le header
	// @todo: A modifier dans le future pour ne cibler que le #main-navigation 20/03/2022
	$(document).find("header nav ul#main-navigation li a, header nav ul li a").not("#add-nav ul li a, .exclude li a, .exclude").each(function(index, element) {
		//$("a", this).each(function(index, element) {
			//data["nav"][i+'-'+index] = {
			data["nav"][index] = {
				href : $.trim($(this).attr('href')),
				text : ($(this).hasClass("view-source")?$(this).text():$(this).html()),
				id : $(this).attr('id') || "",
				class : $(this).attr('class') || "",
				target : $(this).attr('target') || "",
				level : $(element).parents('ul').length
			};
		//});
	});


	// Fonction à exécuter avant la sauvegarde
	$(before_save).each(function(key, funct){ funct(); });

	// On sauvegarde en ajax les contenus éditables
	if(lucide == true)	
	$.ajax({
		type: "POST",
		url: path+"api/ajax.admin.php?mode=update",
		data: data
	})
	.done(function(html) {
		// Affichage/exécution du retour
		$("body").append(html);

		// Fonction à exécuter après la sauvegarde
		$(after_save).each(function(key, funct){ funct(); });

		// S'il y a une fonction de retour
		//if(typeof callback === "function") callback();//@todo voir si utilisé ?
	})
	.fail(function() {
		error(__("Error"));
	});
}


// Changement d'état des boutons de sauvegarde
tosave = function() {	
	// Si site statique on check la modification du header/footer | contenu global
	//console.log($(memo_node).closest("header")) pour les contenu éditable
	// detecter les deplacement du menu
	// detecter le changement de média

	$("#save i").removeClass("fa-spin fa-cog").addClass("fa-floppy");// Affiche l'icône disant qu'il faut sauvegarder sur le bt save	
	$("#save").removeClass("saved").addClass("to-save");// Changement de la couleur de fond du bouton pour indiquer qu'il faut sauvegarder
}


// Champs requis
$.fn.required = function(txt){						
	$(this).addClass("invalid").attr("title", txt).tooltip().tooltip("open").trigger("focus").effect("highlight");
}


// Fonctions pour catcher la sélection
range_selects_single_node = function(range) {
	var start_node = range.startContainer;
	return start_node === range.endContainer && start_node.hasChildNodes() && range.endOffset === range.startOffset + 1;
}

selected_element = function(range) {
	
	// @todo le cas nodeType === 3 ne prend qu'un node en compte et pas toutes la selection
	
	if (range_selects_single_node(range))// La sélection comprend un seul élément
		return range.startContainer.childNodes[range.startOffset];
	else if (range.startContainer.nodeType === 3)// La sélection commence à l'intérieur d'un noeud de texte, donc obtenir son parent
		return range.startContainer.parentNode;
	else// La sélection commence à l'intérieur d'un élément
		return range.startContainer;
}

// Mémorise la sélection pour la retrouver après focus ailleur (toolbox par ex.)
selection = function() {
	// @todo voir si le fait de ne pas raz les memo_ ne crée pas de problème colatéraux...

	memo_selection = window.getSelection();			
	if(memo_selection.anchorNode) {
		memo_range = memo_selection.getRangeAt(0);
		memo_node = selected_element(memo_range);//memo_selection.anchorNode.parentElement memo_range.commonAncestorContainer.parentNode
	}
	else {
		if(typeof memo_range === 'undefined') memo_range = null;
		if(typeof memo_node === 'undefined') memo_node = null;
	}

	//if(dev) console.log("memo_selection", memo_selection);
	//if(dev) console.log("memo_range", memo_range);
	//if(dev) console.log("memo_node", memo_node);
	//if(dev) console.log("memo_range.cloneContents().querySelectorAll('*')", memo_range.cloneContents().querySelectorAll('*'));
}


// Barre d'outil de mise en forme et de contenu
exec_tool = function(command, value) {
	value = value || "";	

	// Sélectionne le contenu car on a perdu le focus en entrant dans les options
	if((command == "CreateLink" || command == "CreateAnchor" || command == "insertImage" || command == "insertIcon" || command == "insertHTML" || command == "insertText") && memo_selection && memo_range) {
		memo_selection.removeAllRanges();
		memo_selection.addRange(memo_range);		
	}

	if(command)
	{
		// Si icône
		if(command == "insertIcon") {
			command = "insertHTML";
			value = "<i class='fa' aria-hidden='true'>&#x"+ value +";</i>";
		}
		
		// Si alignement // Ancienne méthode d'alignement 22-04-2022
		if(/justify/.test(command))
			$(memo_node).removeAttr("align").css("text-align","");


		// Si Ajout d'une ancre
		if(command == "CreateAnchor") { var command_source = command; command = "CreateLink"; }


		// Exécution de la commande
		document.execCommand(command, false, value);
	

		// A sauvegarder	
		tosave();

		// Si on justify on supprime l'éventuel span intérieur // Ancienne méthode d'alignement 22-04-2022
		if(/justify/.test(command))
		{
			// Désélectionne les alignements
			$("[class*='fa-align']").parent().removeClass("checked");			

			// check le bt d'alignement
			$("#align-"+command.match(/justify(.*)/)[1].toLowerCase()).addClass("checked");
			
			// Si il y a un span avec des style on le supprime (chrome)
			if($("span", $(memo_node))[0])
				$("div", memo_node).html($("span", $(memo_node).context.innerHTML).html());
		}

		if(command_source == "CreateAnchor")
		{
			// On supprime le href et le déplace dans name
			$(memo_selection.anchorNode.parentElement).attr("name", $(memo_selection.anchorNode.parentElement).attr("href")).removeAttr("href");

			$("#txt-tool #anchor-option").hide("slide", 300);// Cache le menu d'option avec animation
		}
		else if(command == "CreateLink")
		{
			// Si Target = blank // @todo verif marche sous firefox ??
			if($("#target-blank").hasClass("checked")) memo_selection.anchorNode.parentElement.target = "_blank";
			else $(memo_node).removeAttr("target");

			// Si class bt
			if($("#class-bt").hasClass("checked")) memo_selection.anchorNode.parentElement.classList.add("bt");
			else $(memo_node).removeClass("bt");
			
			$("#txt-tool #link-option").hide("slide", 300);// Cache le menu d'option avec animation
		}
		else if(command == "insertUnorderedList")
		{
			// Sur Chrome le <ul> reste entouré d'un <p>, on le supprime
			if($(memo_node).closest("p").html()) {
				if(dev) console.log("unwrap P");
				$(memo_node).closest("p").replaceWith($(memo_node).closest("p").html())
			}
		}
		else
			$("#txt-tool .option").hide();// Cache le menu d'option rapidement
	}

	$(memo_focus).trigger("focus");// On focus le contenu édité pour faire fonctionner onblur = close toolbox

	// Recrée une sélection en fonction des changements de la dom
	//@todo voir bug FF qd on veut supp un H2 avec html_tool => le focus va sur le <p> précédent créer lors de l'ajout du h2
	//@todo remplacer par la fonction selection();
	memo_selection = window.getSelection();
	memo_range = memo_selection.getRangeAt(0);// @todo debug sous safari lors de l'ajout d'une nouvelle image
	memo_node = selected_element(memo_range);

	// Mise en surbrillance du bouton dans la barre d'outil
	if($(memo_node).closest("li").length) $("#txt-tool #insertUnorderedList").addClass("checked");
	else $("#txt-tool #insertUnorderedList").removeClass("checked");
}


// VIDEO
// Ajoute une vidéo (mqdefault hqdefault maxresdefault) link => mode d'insertion de la vidéo
video = function(link) 
{
	var url_video = $('#txt-tool .option #video').val();

	var lazy = ($(memo_node).closest(".editable").hasClass("lazy")?' loading="lazy"':'');

	// #(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#
	// var id_video = url_video.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);

	var match = url_video.match(/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/);
	var id_video = (match && match[7].length==11)? match[7] : false;

	// Si ajout de lien
	if($("#txt-tool #video-option button i").hasClass("fa-plus")) 
	{
		// Ancienne version non accessible 22/04/2022
		//exec_tool("insertHTML", '<figure role="group" class="fl" style="display: table !important;"><a href="'+ url_video +'" class="video" data-video="'+ id_video +'"><img src="https://img.youtube.com/vi/'+ id_video +'/mqdefault.jpg" alt="" width="320" height="180"'+lazy+'></a><figcaption>'+ __("Caption") +'</figcaption></figure>');

		// Version input image
		//exec_tool("insertHTML", '<div class="video tc" data-video="'+ id_video +'"><input type="image" src="https://img.youtube.com/vi/'+ id_video +'/mqdefault.jpg" title="'+__("Show video")+'"><p id="desc-'+id_video+'">'+ __("Caption") +'</p></div>');


		if(link)
			// Version lien vers Youtube dans un nouvel onglet
			exec_tool("insertHTML", '<figure role="group" class="center"><a href="'+ url_video +'" aria-label="'+__("Show video in new window")+'" target="_blank"><img src="https://img.youtube.com/vi/'+ id_video +'/mqdefault.jpg" alt="" width="320" height="180"'+lazy+'></a><figcaption>'+ __("Caption") +'</figcaption></figure>');
		else
			// Version button lecture dans le site
			exec_tool("insertHTML", '<div class="video tc" data-video="'+ id_video +'"><button title="'+__("Show video")+'"><img src="https://img.youtube.com/vi/'+ id_video +'/mqdefault.jpg" alt="" width="320" height="180"'+lazy+'></button><p id="desc-'+id_video+'">'+ __("Caption") +'</p></div>');


		//$("figure .video").parent().attr('style','');

		tosave();
	}
}


// ANCHOR
// Menu avec les options d'ajout/modif d'ancre
anchor_option = function()
{		
	selection();// Récupère la sélection avant ouverture des options

	$("#unanchor").remove();// Supprime le bouton de supp d'anchor
	$("#txt-tool .option").hide();// Réinitialise le menu d'option

	var name = $(memo_node).closest('a').attr('name');// On récupère le href de la sélection en cours

	// Si lien
	if(name) 
	{
		// Bouton pour supp l'ancre //exec_tool('unanchor');
		$("#txt-tool #anchor-option").prepend("<a href=\"javascript:unanchor();void(0);\" id='unanchor'><i class='fa fa-cancel plt prt' title='"+ __("Remove the link from the selection") +"'></i></a>");

		$("#txt-tool #anchor-option #anchor").val(name);
		$("#txt-tool #anchor-option button span").text(__("Change Anchor"));
		$("#txt-tool #anchor-option button i").removeClass("fa-plus").addClass("fa-floppy");
	}
	else 
	{
		$("#txt-tool #anchor-option #anchor").val('');
		$("#txt-tool #anchor-option button span").text(__("Add Anchor"));
		$("#txt-tool #anchor-option button i").removeClass("fa-floppy").addClass("fa-plus");
	}
	
	$("#txt-tool #anchor-option").show("slide", 300);
}

// Supprime le lien autour
unanchor = function() 
{
	$(memo_node).contents().unwrap();
	$("#txt-tool #anchor-option").hide("slide", 300);

	$(memo_focus).trigger("focus");
}

// Edite ou ajoute l'ancre
edit_anchor = function() 
{
	var anchor = $('#txt-tool .option #anchor').val();

	// Si ajout de lien
	if($("#txt-tool #anchor-option button i").hasClass("fa-plus")) 
		exec_tool('CreateAnchor', anchor)//insertHTML
	else
	{
		$(memo_node).attr("name", anchor);
		
		$("#txt-tool #anchor-option").hide("slide", 300);// Cache le menu d'option avec animation

		tosave();// A sauvegarder

		//@todo voir pour retrouver l'emplacement du focus une fois l'edition fini
	}
}


// LANG
// Menu avec les options d'ajout/modif de lang
lang_option = function()
{		
	selection();// Récupère la sélection avant ouverture des options

	$("#unlang").remove();// Supprime le bouton de supp de lang
	$("#txt-tool .option").hide();// Réinitialise le menu d'option

	var lang = $(memo_node).closest('span').attr('lang');// On récupère la langue de la sélection en cours
	
	old_selection = window.getSelection().toString();// Texte selectionner en cours

	// Si lien
	if(lang) 
	{
		// Bouton pour supp l'ancre //exec_tool('unanchor');
		$("#txt-tool #lang-option").prepend("<a href=\"javascript:unlang();void(0);\" id='unlang'><i class='fa fa-cancel plt prt' title='"+ __("Remove") +"'></i></a>");

		$("#txt-tool #lang-option #lang").val(lang);
		$("#txt-tool #lang-option button span").text(__("Change Language"));
		$("#txt-tool #lang-option button i").removeClass("fa-plus").addClass("fa-floppy");
	}
	else 
	{
		$("#txt-tool #lang-option #lang").val('');
		$("#txt-tool #lang-option button span").text(__("Add Language"));
		$("#txt-tool #lang-option button i").removeClass("fa-floppy").addClass("fa-plus");
	}
	
	$("#txt-tool #lang-option").show("slide", 300);
}

// Supprime le lien autour
unlang = function() 
{
	$(memo_node).contents().unwrap();
	$("#txt-tool #lang-option").hide("slide", 300);

	$(memo_focus).trigger("focus");
}

// Edite ou ajoute la langue
edit_lang = function() 
{
	var lang = $('#txt-tool .option #lang').val();

	// Si ajout de lien
	if($("#txt-tool #lang-option button i").hasClass("fa-plus")) 
	{
		exec_tool('insertHTML', '<span lang="'+lang+'">'+old_selection+'</span>');
	}
	else
	{
		$(memo_node).attr("lang", lang);
		
		$("#txt-tool #lang-option").hide("slide", 300);// Cache le menu d'option avec animation

		tosave();// A sauvegarder

		//@todo voir pour retrouver l'emplacement du focus une fois l'edition fini
	}
}


// LINK 
// Menu avec les options d'ajout/modif de lien
link_option = function()
{		
	selection();// Récupère la sélection avant ouverture des options

	$("#unlink").remove();// Supprime le bouton de supp de lien
	$("#txt-tool .option").hide();// Réinitialise le menu d'option
	$("#txt-tool #link-option #link-text").hide().val('');// Cache et Clean le champs du texte du lien
	$("#target-blank").removeClass("checked");// Réinitialise la colorisation du target _blank
	$("#class-bt").removeClass("checked");// Réinitialise la colorisation du class bt

	var href = $(memo_node).closest('a').attr('href');// On récupère le href de la sélection en cours

	// Si lien
	if(href) 
	{
		// Si target = blank
		if(memo_node.target == "_blank") $("#target-blank").addClass("checked");

		// Si class bt
		if($(memo_node).hasClass("bt")) $("#class-bt").addClass("checked");

		$("#txt-tool #link-option #link").val(href);
		$("#txt-tool #link-option #link-text").val($(memo_node).closest('a').text().trim()).show();

		// Cache le bouton, car édition en live
		$("#txt-tool #link-option button").hide();
		//$("#txt-tool #link-option button span").text(__("Change Link"));
		//$("#txt-tool #link-option button i").removeClass("fa-plus").addClass("fa-floppy");
	}
	else 
	{
		$("#txt-tool #link-option #link").val('');

		// Affiche le bouton en mode ajout
		$("#txt-tool #link-option button").show();
		$("#txt-tool #link-option button span").text(__("Add Link"));
		$("#txt-tool #link-option button i").removeClass("fa-floppy").addClass("fa-plus");
	}
	
	// Bouton pour supp le lien //exec_tool('unlink');	
	$("#txt-tool #link-option").prepend("<a href=\"javascript:unlink();void(0);\" id='unlink'><i class='fa fa-cancel plt prt' title='"+ __("Remove the link from the selection") +"'></i></a>");

	// Affichage des options pour le lien et repositionnement
	// 300, car "slide" Crée un bug du chargement de l'autocomplete
	/*$("#txt-tool #link-option").show(300, function() {
		toolbox_height = $("#txt-tool").outerHeight();
		this_top_scroll = this_top - toolbox_height - 12;
		toolbox_position(this_top_scroll, this_left);
	});*/

	// Afficher les options et le fond (table)
	$("#txt-tool #link-option").css("display","table");
}

// Supprime le lien autour
unlink = function() 
{
	$(memo_node).closest("a").contents().unwrap();
	$("#txt-tool #link-option").hide("slide", 300);

	$(memo_focus).trigger("focus");
}

// @todo: SUPP 01/2023 / Plus utiliser car tout se passe dans l'edition à la volé des liens 
// Edite ou ajoute le lien
/*link = function() 
{
	var link = $('#txt-tool .option #link').val();

	// Si ajout de lien
	if($("#txt-tool #link-option button i").hasClass("fa-plus")) 
		exec_tool('CreateLink', link)
	else
	{
		$(memo_node).closest("a").attr("href", link);

		// Si Target = blank
		if($("#target-blank").hasClass("checked")) $(memo_node).closest("a").attr("target","_blank");
		else $(memo_node).closest("a").removeAttr("target");	

		// Si class bt
		if($("#class-bt").hasClass("checked")) $(memo_node).closest("a").addClass("bt");
		else $(memo_node).closest("a").removeClass("bt");
		
		$("#txt-tool #link-option").hide("slide", 300);// Cache le menu d'option avec animation

		tosave();// A sauvegarder

		//@todo voir pour retrouver l'emplacement du focus une fois l'edition fini
	}
}*/


// Si target blank
target_blank = function(mode) {
	if(mode == true || !$("#target-blank").hasClass("checked")) 
	{
		$("#target-blank").addClass("checked");

		// Si lien existe on ajoute le target _blank
		if(!$("#txt-tool #link-option button i").is(":visible"))
		{
			$(memo_node).closest("a").attr("target","_blank");
			tosave();
		}
	}
	else
	{
		$("#target-blank").removeClass("checked");

		// Si lien existe on supp le target _blank
		if(!$("#txt-tool #link-option button i").is(":visible"))
		{
			$(memo_node).closest("a").removeAttr("target");
			tosave();
		}
	}
}

// Si class bt
class_bt = function(mode) {
	if(mode == true || !$("#class-bt").hasClass("checked")) 
	{
		$("#class-bt").addClass("checked");

		// Si lien existe on ajoute la class bt
		if(!$("#txt-tool #link-option button i").is(":visible"))
		{
			$(memo_node).closest("a").addClass("bt");
			tosave();
		}
	}
	else 
	{
		$("#class-bt").removeClass("checked");

		// Si lien existe on supp la class bt
		if(!$("#txt-tool #link-option button i").is(":visible"))
		{
			$(memo_node).closest("a").removeClass("bt");
			tosave();
		}
	}
}

// Mets le contenu dans un bloc de mise en avant
highlight = function(){

	var closest = $(memo_node).closest(".highlight");//parents

	// Si on est déjà dans un highlight on le supprime
	if(closest.length) {
		if(closest.prop("tagName") == 'P') {
			if(dev) console.log("P removeClass highlight");
			closest.removeClass("highlight");
		}
		else {
			if(dev) console.log("Remove DIV with highlight");

			$("#tool-highlight").removeClass("checked");

			if(closest.html()) {
				if(dev) console.log("replaceWith html");
			 	closest.replaceWith(closest.html());// Chrome
			}
			else {
				if(dev) console.log("replaceWith closest text");
				closest.closest(html).replaceWith(closest.text());// FF
			}
		}
	}
	else 
	{
		if(dev) console.log("highlight");

		$("#tool-highlight").addClass("checked");
			
		// Si plus d'un node selectionné on utilise la méthode complexe
		if(memo_range.cloneContents().querySelectorAll('*').length>1)
		{
			if(dev) console.log("highlight multiple node");
			
			// Si le parent est un ul on wrap dans le highlight et stop l'execution
			//if(!$(memo_range.commonAncestorContainer).hasClass("editable"))
			if(memo_range.commonAncestorContainer.nodeName == "UL")
			{
				if(dev) console.log("highlight parent node");

				$(memo_range.commonAncestorContainer).wrap('<div class="highlight"></div>');

				return;
			}

			// Contenu parent éditable
			var commonAncestorContainer = memo_range.commonAncestorContainer;

			// Mémorise la selection pour connaitre la position des nodes de début et de fin de la selection
			var clone_range = memo_range.cloneRange();
			//if(dev) console.log("clone_range", clone_range);

			// Etand la selection au node qui entoure la selection de début et de fin
			memo_range.setStartBefore(memo_range.startContainer);
			memo_range.setEndAfter(memo_range.endContainer);

			// Création du div avec la class highlight
			var highlight = document.createElement("div");
			highlight.className = "highlight";

			// Déplace le contenu selectioné dans le div highlight
			highlight.appendChild(memo_range.extractContents());
			
			// Inject le div highlight avec le contenu a l'emplacement de la selection
			memo_range.insertNode(highlight);

			// Supprime les nodes qui contenais le début et fin de selection car ils restent en resident dans la page (ils ne sont pas extrait, seul le texte et node dans la selection l'est)
			// Regarde aussi si le parent supprimé n'est pas le bloc éditable, mais bien l'élément restant
			if(clone_range.startContainer.parentNode == commonAncestorContainer) 
				clone_range.startContainer.remove();
			else if(!$(clone_range.startContainer).hasClass("editable"))
				clone_range.startContainer.parentNode.remove();
			
			if(clone_range.endContainer.parentNode == commonAncestorContainer)
				clone_range.endContainer.remove();
			else if(!$(clone_range.endContainer).hasClass("editable"))
				clone_range.endContainer.parentNode.remove();

			// Clean les range
			//memo_selection.removeAllRanges();
			//memo_selection.addRange(clone);

		}
		else// Qu'un node selectionnée on le wrap dans le highlight
		{
			if(dev) console.log("highlight one node");

			var node = $(memo_node);

			// Si l'élément n'est pas un P (un a ou b par exemple), on prend l'élément du dessu
			if($(memo_node).prop("tagName") != "P" && $(memo_node).parent().prop("tagName") == "P") {
				if(dev) console.log("parent");
				node = $(memo_node).parent();
			}
			
			node.wrap('<div class="highlight"></div>');
		}
	}
}

// Ajout/Suppression d'une class
grid = function(col){
	var grid = "<div class='grid'>";
	for(i=1; i<=col ;i++) grid+= "<div></div>";
	grid+= "</div>";

	exec_tool('insertHTML', grid);
}

// Ajout/Suppression d'une class
class_tool = function(theClass){
	// Si on est déjà dans un élément avec la class demandé : on supp la class
	if($(memo_node).closest("p, div, h2, h3, h4").hasClass(theClass))
	{
		$(memo_node).closest("p, div, h2, h3, h4").removeClass(theClass);
		$("#tool-"+theClass).removeClass("checked");
	}
	else 
	{
		// Si alignement => Supprime les alignement avant l'ajout du nouveau
		if(["tl", "tc", "tr"].indexOf(theClass) > -1) 
		{
			$(memo_node).closest("p, div, h2, h3, h4").removeClass("tl tc tr");
			$(memo_node).closest("p, div, h2, h3, h4").removeAttr("align").css("text-align","");// Ancien alignement

			// Désélectionne les alignements
			$("[class*='fa-align']").parent().removeClass("checked");	
		}

		$(memo_node).closest("p, div, h2, h3, h4").addClass(theClass);
		$("#tool-"+theClass).addClass("checked");
	}
}

// Ajout/Suppression d'un element html en block
html_tool = function(html){
	// Si on est déjà dans un élément entouré du 'HTML' demandé : on le supp
	if($(memo_node).closest(html).length)
	{
		$("#"+html).removeClass("checked");

		if($(memo_node).closest(html).html()) {
			if(dev) console.log("replaceWith html");
		 	$(memo_node).closest(html).replaceWith($(memo_node).closest(html).html());// Chrome
		}
		else {
			if(dev) console.log("replaceWith closest text");
			$(memo_node).closest(html).replaceWith($(memo_node).text());// FF
		}
	}
	else 
	{
		if(dev) console.log("formatBlock");

		$("#txt-tool button").removeClass("checked");
		$("#"+html).addClass("checked");

		exec_tool('formatBlock', html);
	}
}

// Ajout/Suppression d'un element html dans un contenu
html_tool_inline = function(tag){
	// Si on est déjà dans un élément entouré du 'HTML' demandé : on le supp
	if($(memo_node).is(tag)) {
		$("#"+tag).removeClass("checked");
		$(memo_node).replaceWith($(memo_node).html());
	}
	else {
		// Ajoute la balise avec la class de couleur
		var selection_string = window.getSelection().toString();
		var add_tag = '<'+tag+'>' + selection_string + '</'+tag+'>';
		exec_tool('insertHTML', add_tag);
	}	
}

// Colorise un texte
color_text = function(color){
	// Si on est déjà dans un élément entouré du 'HTML' demandé : on le supp
	if($(memo_node).is("em[class^=color-]")) {
		$("."+color).removeClass("checked");
		$(memo_node).replaceWith($(memo_node).html());
	}
	else {
		// Ajoute la balise avec la class de couleur
		var selection_string = window.getSelection().toString();
		var add_class = '<em class="'+color+'">' + selection_string + '</em>';
		exec_tool('insertHTML', add_class);
	}	
}

// Voir le code source
view_source = function(memo, force){

	// Si on est déjà en mode view source on remet en html
	if($(memo).hasClass("view-source")) 
	{
		$("#view-source").removeClass("checked");
		$(memo).removeClass("view-source").html($(memo).text());
	}
	else 
	{
		// Nettoie les retours la ligne
		$(memo).html($(memo).html().replace(/\n/g, ""));

		// Ajout des retours à la ligne qui vont biens
		$("div:first", memo).before("\n"); // Premier div
		$(memo).html(// Les autres div fermant et les double div qui démarre une imbrication
			$(memo).html()
				.replace(/<br>/ig, "<br>\n")
				.replace(/<\/div>/ig, "<\/div>\n")
				.replace(/<div><div>/ig, "<div>\n<div>")
		);

		// @todo ne fait qu'un niveau d'imbrication
		// Tabulation sur les imbrications
		$("div", memo).each(function(event) {
			if($(this).children().length > 1) $(this).children().before("\t")//\t
		});

		// Passe en mode source
		$("#view-source").addClass("checked");
		$(memo).addClass("view-source").text($(memo).html())//.html();
	}
}



// Dialog box avec effet de transfert
dialog = function(mode, source, target, callback) {

	// @todo: faire en sorte que la dialog fadeIn et fadeOut lorsqu'elle apparaît/disparaît. Pas juste visibility:hidden/visible...

	$.ajax({
			type: "POST",
			url: path+"api/ajax.admin.php?mode=dialog-"+mode, 
			data: {
				"target": target,
				"source": (target == "bg" ? $(source).attr("data-id") : source.id),
				"width": $(source).data("width") || "",
				"height": $(source).data("height") || "",
				"dir": $(source).data("dir") || "",
				"nonce": $("#nonce").val()
			}
		})
		.done(function(html){

			// Pour ne pas instancier plusieurs fois la dialog
			$(".dialog-"+mode).remove();
			
			// Création de la dialog invisible
			$("body").append(html);

			// Si l'ajax renvoie bien la dialog demandée
			if($(".dialog-"+mode).length) 
			{			
				// Instanciation de la dialog en mode invisible
				$(".dialog-"+mode).dialog({
					modal: true,
					autoOpen: false,
					width: "80%",
					close: function()
					{
						$(".dialog-"+mode).remove();

						$("body").off(".dialog-escape");// Unbind la fermeture avec touche echape
						
						// S'il y a une fonction de callback on l'exécute
						if(typeof callback === "function") callback();
					},
					create: function() 
					{
						if(mode == "media") 
						{
							// Création des onglets
							$(".dialog-media").tabs({
								beforeLoad: function(event, ui) {
									ui.ajaxSettings.url += ( /\?/.test(ui.ajaxSettings.url) ? "&" : "?" ) + 'nonce=' + $("#nonce").val();
								}
							});

							// Place les onglets à la place du titre de la dialog
							$(".dialog-media").siblings(".ui-dialog-titlebar").children(".ui-dialog-title").html($(".ui-tabs-nav")).parent().addClass("ui-tabs");

							// Place le moteur de recherche de media dans le titre de la dialog
							$("#recherche-media").detach().insertBefore(".dialog-media")//.prependTo(".ui-dialog");
						}
					},
					resize: function(event, ui) 
					{
						// Masque le texte dans les tab si on réduit trop la fenêtre
						if(ui.size.width < 635) $(".ui-tabs-nav span").hide();
						else $(".ui-tabs-nav span").show();
					}						
				});

				$(".dialog-"+mode).dialog("open");

				// Si touche échape on ferme la dialog
				$("body").on("keydown.dialog-escape", function(event) {
					if(event.keyCode === $.ui.keyCode.ESCAPE) $(".dialog-"+mode).dialog('close');
					event.stopPropagation();
				});;	
			}
		});
}


// Ouvre la fenêtre pour ajouter une image/fichier dans la galerie des medias (intext, isolate, bg)
media = function(source, target) {
	//$(memo_focus).trigger("focus");// On focus le contenu édité pour faire fonctionner onblur = close toolbox
	
	// Dialog de gestion des medias
	dialog("media", source, target, function() {					
			// Unbind le drag&drop pour l'ajout de média
			$("body").off(".dialog-media");

			// Relance les autres events
			editable_event();
			editable_media_event();
			body_editable_media_event();
		}
	);
}


// Upload d'un média
upload = function(source, file, resize)
{
	uploading = true;

	// Type de fichier supporté pour l'upload
	var mime_supported = [
		"image/jpg","image/jpeg","image/pjpeg","image/png","image/x-png","image/gif","image/x-icon",
		"application/pdf","application/zip","application/x-zip-compressed","text/plain"		
	];

	var width = $(source).data("width") || "";
	var height = $(source).data("height") || "";
	var data_class = $(source).data("class") || "";
	var dir = $(source).data("dir") || "";

	var domain_path = window.location.origin + path;

	if(file) 
	{
		//if(mime_supported.indexOf(file.type) > 0)// C'est bien un fichier supporté
		{				
			// @todo: ajouter un cog loading en sur l'image (a coter du % ?)

			// Layer pour la progressbar
			var progressid = "progress-" + source.attr("id");
			source.append("<div id='"+progressid+"' class='progress bg-color small' style='height: "+source.outerHeight()+"px;'></div>");			
			
			// Type mime du fichier
			var mime = (file.type ? file.type.split("/") : "");

			// Supprime les fichiers autres que image
			$("> .fa", source).remove();

			// Affiche la preview si image
			if(mime[0] == "image") 
			{
				// Si pas de tag img on le crée
				if($("img", source).html() == undefined) {
					if(width || height) 
						var style = " style='"+(width?" max-width:"+width+"px;":"")+(height?"max-height:"+height+"px;":"")+"'";
					else 
						var style = null;	

					$(source).append('<img'+(style)+(data_class?' class="'+data_class+'"':'')+'>');
				}

				// On fade à moitié (50%)
				$("img", source).addClass("to50");

				var reader = new FileReader();
				reader.onload = function(theFile) {
					// On crée un objet image pour s'assurer que l'image est bien chargée dans le browser avant de prendre sa taille pour le layer de progression d'upload
					var image = new Image();
					image.src = theFile.target.result;

					image.onload = function() {// Image bien chargée dans le navigateur
						if($("#"+progressid).length) {			
							// Si l'upload n'est pas déjà fini on calle la source et la hauteur de la progress bar
							if(!$("img", source).attr("src")) {								
								$("img", source).attr("src", this.src);// On colle le bin de l'image dans le src
								$("#"+progressid).css("height", source.outerHeight()+"px");// On force la taille de progression d'upload
							}
						}
					};	
				}
				reader.readAsDataURL(file);
			}

			// Upload du fichier avec progressbar
			var data = new FormData();
			data.append("file", file);
			data.append("index", file.name);
			data.append("width", width);
			data.append("height", height);
			data.append("dir", dir);
			if(resize) data.append("resize", resize);
			data.append("nonce", $("#nonce").val());

			$.ajax({
				type: "POST",
				url: path+"api/ajax.admin.php?mode=add-media",
				xhr: function() {
					var xhr = $.ajaxSettings.xhr();
					if(xhr.upload) {									
						xhr.upload.addEventListener("progress", function (event) {// Progressbar
							if(event.lengthComputable){
								p100 = (event.loaded * 100 / event.total);
								$("#"+progressid).css("width", p100+"%").css("height", source.outerHeight()+"px").html(Math.floor(p100) + "%");//.toFixed(2)
							}
						}, false);
					}
					return xhr;
				},
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(media)
				{					
					if(media.match('dialog-connect') || media.match('error'))// Si erreur ou problème de login
					{
						source.hide("slide", 300);
						$("body").append(media);
					}
					else if(media)// ça renvoi un fichier
					{
						source.removeClass("uploading");// Supprime le spin d'upload

						$("#"+progressid).css("width", "100%").css("height", source.outerHeight()+"px").html("100%");// Pour être sur d'afficher 100%
						
						// Si c'est une image
						if(mime[0] == "image") 
						{
							$("img", source).removeClass("to50");// On remet l'image à l'opacité normale
							$("img", source).attr("src", domain_path + media);// Affiche l'image finale 
						}
						else if(!source.attr("data-media"))// Si c'est un fichier autre et isolé
						{						
							$("img", source).remove();// Supprime les images
							$("[class*='fa-file']", source).remove();// Supprime les fichier déjà présent

							// On crée un bloc fichier
							$(source).append('<i class="fa fa-fw fa-doc mega" title="'+ media +'"></i>');	
						}
						
						// Nom du fichier final si dialog médias
						if(source.attr("data-media")) {
							source.attr("data-media", media);// Pour la manipulation (path + media)
							$(".file div", source).html(media.split('/').pop());// Pour l'affichage 
							$(".copy input", source).val(path + media);// Pour copier le nom du fichier
						}				

						// Détruis le layer de progressbar
						$("#"+progressid).fadeOut("medium", function() { 
							this.remove();
							source.addClass("uploaded");// Icone uploaded avec fadeinout
						});

						$(".supp", source).css("visibility","visible");// Affiche l'option de suppression

						if(!$(".dialog-media").length) tosave();// Mode : A sauvegarder
					}
					else {
						source.hide("slide", 300);
						error(__("Error"));
					}
					
					// Lance l'upload suivant
					if(typeof source_queue !== 'undefined' && source_queue.length > 0 && file_queue.length > 0)
						upload(source_queue.shift(), file_queue.shift());
					else
						uploading = false;// Fin des uploads
				}
			});

		}
		/*else {
			source.hide("slide", 300);
			error(__("This file format is not supported") + " : "+ file.type);
		}*/

		//console.log(data.files[0].type);
	}   	
}


// Insertion d'un fichier depuis la dialog media
get_file = function(id)
{	
	$(".dialog-media li").css("opacity","0.4");
	$("#"+id).css("opacity","1");

	if($("#dialog-media-target").val() == "isolate")// Insert dans un bloc isolé
	{	
		// Supprime les images
		$("#"+$("#dialog-media-source").val()+" img").remove();

		// Supprime les vidéos
		$("#"+$("#dialog-media-source").val()+" video").remove();

		// Supprime les fichiers
		$("#"+$("#dialog-media-source").val()+" > .fa").remove();

		if($("#"+id).attr("data-type") == 'video')
			// Ajoute le fichier
			$("#"+$("#dialog-media-source").val()).append('<video src="'+ $("#"+id).attr("data-media") +'" preload="none" controls></video>');	
		else
			// Ajoute le fichier
			$("#"+$("#dialog-media-source").val()).append('<i class="fa fa-fw fa-doc mega" title="'+ $("#"+id).attr("data-media") +'"></i>');	
	}
	else// Insertion du lien vers le fichier dans bloc texte
		exec_tool("insertHTML", '<a href="'+ path + $("#"+id).attr("data-media") +'">'+ $("#"+id).attr("data-media").split('/').pop() +' ('+ $("#"+id+" .infos").text() +')</a>');

	// Fermeture de la dialog
	$(".dialog-media").dialog("close");
	
	tosave();// A sauvegarder
}


// Insertion d'une image depuis la dialog media
get_img = function(id, link)
{	
	//@todo ajouter un loading pour informer que l'on resize l'image avant fermeture de la dialog
	//@todo voir pour faire un fade opacity en js, car en css ça fait un bug lors de l'ouverture de la dialog
	
	// Focus sur l'image choisie
	$(".dialog-media li").css("opacity","0.4");
	$("#"+id).css("opacity","1");
	
	var width = $("#dialog-media-width").val();
	var height = $("#dialog-media-height").val();

	var media_source = $("#dialog-media-source").val();

	var crop = $("#"+media_source).hasClass('crop');
	
	var data_class = $("#"+media_source).data("class") || "";

	// Si id ou data-id pour les bg()
	var dir = $("#"+media_source).data("dir") || ($("[data-id='"+media_source+"']").data("dir") || "");

	var domain_path = window.location.origin + path;

	// Image en lazyloading dans un contenu éditable
	var lazy = ($("#"+media_source).hasClass("lazy")?' loading="lazy"':'');


	// Resize de l'image et insertion dans la source
	$.ajax({
		type: "POST",
		url: path+"api/ajax.admin.php?mode=get-img",
		data: {
			"img": $("#"+id).attr("data-media"),
			"width": width,
			"height": height,
			"dir": dir,
			"crop": crop,
			"nonce": $("#nonce").val()
		},
		success: function(final_file)
		{ 
			if($("#dialog-media-target").val() == "isolate")// Insert dans un bloc isolé
			{
				// Si pas encore de tag img
				if($("#"+media_source+" img").html() == undefined)
				{
					// Supprime les fichiers
					$("#"+media_source+" > .fa").remove();

					// Ajoute l'image
					$("#"+media_source).append('<img src="'+ domain_path + final_file +'" alt=""'+
						(width || height?' style="'+
								(width?'max-width: '+width+'px;':'') + (height?'max-height: '+height+'px;':'')
							+'"':'')+
						(data_class?' class="'+data_class+'"':'')+'>');
				}
				else
					$("#"+media_source+" img").attr("src", domain_path + final_file);
			}
			else if($("#dialog-media-target").val() == "intext")// Ajout dans un contenu texte
			{
				if(typeof link !== 'undefined' && link)// Avec lien zoom
					exec_tool("insertHTML", '<a href="'+ $("#"+id).attr("data-media") +'"><img src="'+ domain_path + final_file +'" alt="" class="fl"'+lazy+'></a>');
				else// Juste l'image
					exec_tool("insertHTML", '<img src="'+ domain_path + final_file +'" alt="" class="fl"'+lazy+'>');				
			}
			else if($("#dialog-media-target").val() == "bg")// Modification d'un fond
			{
				var dataidsource = "[data-id='"+media_source+"']";
				$(dataidsource).attr("data-bg", domain_path + final_file);
				$(dataidsource).css("background-image", "url("+ domain_path + final_file +")");

				// Ajout du bt supp si pas existant
				if(!$(dataidsource+" .clear-bg").length)
				$(dataidsource+" .bg-tool").prepend(clearbg_bt);
			}

			// Fermeture de la dialog
			$(".dialog-media").dialog("close");
			
			tosave();// A sauvegarder
		}
	});
}

// Alignement de l'image intext
img_position = function(align) {
	var figure = $(memo_img).closest("figure");

	// Si l'image est dans une figure
	if(figure.length) 
		if(figure.hasClass(align)) figure.removeClass(align);
		else figure.removeClass("center fl fr").addClass(align);
	else 
		if($(memo_img).hasClass(align)) $(memo_img).removeClass(align);
		else $(memo_img).removeClass("center fl fr").addClass(align);
}

// Pour ajouter une légende sous l'image
img_figure = function() {
	// Si on est déjà dans un élément entouré du 'figure & figcaption' => on les supp
	if($(memo_img).closest("figure").length)
	{
		$("#figure").removeClass("checked");

		// Récupère les class de figure pour la remettre sur l'image
		var figure_class = $(memo_img).closest("figure").attr("class");
		$(memo_img).addClass(figure_class);

		// Supprime figure & figcaption
		$(memo_img).parent(".editable .ui-wrapper").unwrap().next("figcaption").remove();
	}
	else 
	{
		$("#figure").addClass("checked");

		// Récupère les class de l'image pour les mettre sur figure
		var img_class = $(memo_img).removeClass("ui-resizable").attr("class");
		$(memo_img).removeClass("center fl fr")

		// Ajoute la figure et le figcaption
		$(memo_img).parent(".editable .ui-wrapper")
	   		.after("<br>")// Pour pouvoir ajouter des contenus à la suite de la figure
	    	.wrap('<figure role="group" class="'+img_class+'" aria-label="'+ __("Caption") +'" />')
	    	.after("<figcaption>"+ __("Caption") +"</figcaption>");
	}
}

// Supprime l'image sélectionnée du contenu
img_remove = function() {
	$(memo_img).remove();
	$("#img-tool").remove();
	memo_img = null;
}

// Transfert width/height du style vers la dom img
img_transfert_style = function(event) {
	var width = $(event).css('width');
	var height = $(event).css('height');
	if(width && height) {
		$(event).attr('width', parseInt(width));
		$(event).attr('height', parseInt(height));
		$(event).removeAttr('style');
	}
	$(event).removeClass("ui-resizable");
}

// Si on focus-out/drag-leave d'une image dans bloc éditable
img_leave = function() 
{
	// Supprime la barre d'outil image
	$("#img-tool").remove();

	// Supprime le resizer s'il y en a un
	if($('.editable .ui-wrapper img').length) $('.editable .ui-wrapper img').resizable('destroy');
	else if($('.editable .ui-wrapper').length) $('.editable .ui-wrapper').remove();
		
	// Supprime le style sur l'image sélectionnée et le transfert sur la dom de l'image
	if(memo_img) {
		img_transfert_style(memo_img);
		memo_img = null;
	}
	else {		
		$(".editable img").each(function() {
			img_transfert_style(this);
		});
	}
}

// Retourne le poids d'un fichier
filesize = function(file) {

    var request = new XMLHttpRequest();

    request.open("HEAD", file, false);

    request.send(null);//200

    //console.log(request);

	//request.getResponseHeader('content-length')
    /Content\-Length\s*:\s*(\d+)/i.exec(request.getAllResponseHeaders());
    return Math.ceil(parseInt(RegExp.$1) / 1024);// Taille en Ko
}

// Optimise l'image à la demande
img_optim = function(option, that) {

	// Loading
	$(that).html("<i class='fa fa-cog fa-spin'></i>");

	// Disable les bt d'optim
	$(".dialog-optim-img .bt").attr("onclick","");

	// L'image
	img = $("img", $(that).parent());

	// Scroll jusqu'a l'image
	scrollToImg(img);

	// Donnée sur l'image
	src = $(img).attr("src");

	var width = imgs[src]['width'];
	var height = imgs[src]['height'];

	original_filesize = imgs[src]['size'];// Poids de l'image d'origine

	var domain_path = window.location.origin + path;// Domaine complet

	src = src.replace(domain_path, "");// Supprime le domaine du nom de l'image
	
	var regex_media_dir = new RegExp(media_dir+"/", "g");

	var img_nomedia = src.replace(regex_media_dir, "").replace(/resize\//, "");// Chemin sans media

	// Si le chemin contien un dossier
	if(img_nomedia.indexOf("/") !== -1) 
	{
		var img_name = src.split("?")[0].split('/').pop();// nom-image.ext sans ce qu'il y a après "?" et après le dossier "/"
		var dir = img_nomedia.split("/"+img_name).shift();// Prends la première partie avant le nom de l'image
	}
	else var dir = "";

	// Resize de l'image et remplacement
	$.ajax({
		type: "POST",
		url: path+"api/ajax.admin.php?mode=get-img",
		data: {
			"img": src,
			"width": width,
			"height": height,
			"dir": dir,
			"option": option,
			"nonce": $("#nonce").val()
		},
		success: function(final_file)
		{ 
			// Détection du poids de la nouvelle image
			new_filesize = filesize(domain_path + final_file);

			// Infobulle sur le gain de poids
			if($.isNumeric(original_filesize))
				light(Math.round((original_filesize - new_filesize)*100/original_filesize) +"% d'économie<div class='grey small'>"+original_filesize+"Ko => "+new_filesize+"Ko</div>", 2500);
			else
				light("Nouvelle taille de l'image : "+new_filesize+"Ko", 2500);

			// Affectation de la nouvelle image
			if(imgs[src]['type'] == 'bg')
				$('[data-bg$="'+src+'"]').attr({
					"data-bg": domain_path + final_file,
					"style": "background-image: url('"+domain_path + final_file+"')"
				});
			else
				$('img[src$="'+src+'"]').attr("src", domain_path + final_file);		
		
			tosave();// A sauvegarder

			// On recharge les optimisations d'image
			setTimeout(function() {// Timeout pour eviter la cache navigateur sur les widthXheight
				img_check();
			}, 1000);			
		}
	});
}


// Scroll jusqu'a une image
scrollToImg = function(that){
	
	//@todo test le cas ou 2 fois la meme image dans le contenu

	// si c'est une image en fond
	if($(that).hasClass('bg'))
		var scrollTo = $('[data-bg$="'+$(that).attr("src")+'"]').offset().top - $("#admin-bar").height();
	else
		var scrollTo = $('img[src$="'+$(that).attr("src")+'"]').offset().top - $("#admin-bar").height();	

	var scrollTo = (scrollTo > $("#admin-bar").height() ? scrollTo : 0);

	$root.animate({ scrollTop: scrollTo	}, 300, "linear");
}

// Liste les images dans la page pour suggérer des optimisations
img_check = function(file) 
{
	// Init Variable
	imgs_optim = "";
	imgs = {};
	var imgs_size = 0;
	host = location.protocol +'//'+ location.host + path;

	// Supprime les anciennes occurrences de la fenêtre
	$(".dialog-optim-img").dialog('close');

	// Liste les images dans des zone média, les contenu éditables, bg
	$(document).find("main .editable-media img, main .editable img, main [data-id][data-bg]").each(function()
	{
		if($(this).hasClass("editable-bg")) {// Image en background
			var src = path + $(this).attr("data-bg").replace(host, "");
			if(src) {
				imgs[src] = {};
				imgs[src]['type'] = 'bg';
			}

			// Taille de l'image
			/*var bg = new Image();
		    bg.src = item.css('background-image').replace(/url\(|\)$|"/ig, '');
			imgs[src]['width'] = bg.width;
			imgs[src]['height'] = bg.height;*/
		}
		else {// Image dans contenu éditable ou fonction media
			var src = $(this).attr("src").replace(host, "");// path + => crée un bug pour les images dans les contenus
			if(src) {
				imgs[src] = {};
				imgs[src]['type'] = 'img';


				// Taille dans la dom
				imgs[src]['width'] = $(this)[0].width;//clientWidth
				imgs[src]['height'] = $(this)[0].height;

				// Taille réel de l'image
				imgs[src]['naturalWidth'] = $(this)[0].naturalWidth;
				imgs[src]['naturalHeight'] = $(this)[0].naturalHeight;
			}
		}
	});

	//console.log(imgs);


	// S'il y a des images
	if(Object.keys(imgs).length > 0)
	{
		// Liste les images
		var num = 0;
		$.each(imgs, function(src, img)
		{
			// Si l'image est en local && existe (sa taille original est !=0)
			if(src.match(/(http(s)?:\/\/)?/)[0] == '' && img.naturalWidth != 0)
			{
				var optimize = '';

				// extraction de l'Extention
				var ext = /(?:\.([^.]+))?$/.exec(src.split("?")[0])[1];

				// c'est bien une image et pas un svg
				if(ext != "svg")
				{
					// extraction de la Taille
					var size = filesize(src.split("?")[0]);

					// Si Taille d'image
					if(!isNaN(size))
					{
						imgs[src]['size'] = size;

						// total des poids d'image
						imgs_size = imgs_size + size;

						// Image dans le contenu
						if(img.type == 'img')
						{
							// Vérifie la taille de l'image pour proposer une optimisation
							var widthRatio = (img.width / img.naturalWidth) * 100;
							var heightRatio = (img.height / img.naturalHeight) * 100;

							// Image + grande que la zone afficher => Redimentionnement
							if(widthRatio < 80 || heightRatio < 80)
								optimize = "<a href='javascript:void(0)' onclick=\"img_optim('resize', this)\" class='bt vam' style='padding: 0 .5rem'>"+__("Resize")+"</a> ";
						}

						// Si c'est un png & lourd => Conversion en jpg (alpha => blanc)
						if(ext == 'png' && size > img_green) {
							optimize+= "<a href='javascript:void(0)' onclick=\"img_optim('tojpg', this)\" class='bt vam' style='padding: 0 .5rem'>"+__("Convert to")+" jpg</a> ";
							
							// Si c'est un png & lourd & option webp => Conversion en webp (alpha conservé)
							if(typeof towebp != 'undefined') 
								optimize+= "<a href='javascript:void(0)' onclick=\"img_optim('towebp', this)\" class='bt vam' style='padding: 0 .5rem'>"+__("Convert to")+" webp</a> ";
						}

						

						// Si jpg & lourd => compression //@todo preview avec choix du taux de compression
						/*if(ext == 'jpg' && size > img_warning)
							optimize+= "<a href='javascript:void(0)' onclick=\"img_optim('compress', this)\" class='bt small vam' style='padding: 0 .5rem'>"+__("Compress")+"</a> ";*/

						// Couleur de vigilance
						if(size <= img_green) var imgcolor = 'green';
						else if(size > img_green && size < img_warning) var imgcolor = 'orange';
						else if(size >= img_warning) var imgcolor = 'red';

						// Pour l'affichage
						imgs_optim += "<li class='"+imgcolor+" pbt'><img src='"+src+"' width='50' class='pointer "+img.type+"' onclick='scrollToImg(this)' title='"+src.split("?")[0] +" | "+ (imgs[src]['naturalWidth']?imgs[src]['naturalWidth']+"x"+imgs[src]['naturalHeight']+"px":__("Background"))+"'> ["+ext+"] <span class='size'>"+size+"Ko</span> "+optimize+"</li>";

						++num;
					}
				}
			}
		});


		// Si des images à optimiser
		if(imgs_optim)
		{
			// Statistique final

			// Poids
			if(imgs_size <= imgs_green) var sizecolor = 'green';
			else if(imgs_size > imgs_green && imgs_size < imgs_warning) var sizecolor = 'orange';
			else if(imgs_size >= imgs_warning) var sizecolor = 'red';

			// Nombre d'image
			if(num < imgs_num) var numcolor = 'green';
			else if(num == imgs_num) var numcolor = 'orange';
			else if(num > imgs_num) var numcolor = 'red';


			// Affichage de la dialog avec les images a optimisé
			$("body").append("<div class='dialog-optim-img' title='"+__("Image optimization")+"'><ul class='pan unstyled smaller'>"+imgs_optim+"</ul><div class='ptt smaller bold'><span class='"+numcolor+"' title='"+__("Limit")+" "+imgs_num+"'>"+num+" images</span> = <span class='"+sizecolor+"' title='"+__("Limit")+" "+imgs_warning+"Ko'>"+imgs_size+"Ko</span></div></div>");

			// Dialog en layer 
			// "right-10 top", at: "left bottom+10"
			// "left top", at: "left+10 bottom+10"
			$(".dialog-optim-img").dialog({
				autoOpen: false,
				width: 'auto',
				maxHeight: 500,
				position: { my: "right top", at: "right bottom+10", of: $("#admin-bar") },
				show: function() {$(this).fadeIn(300);},
				close: function() { $(".dialog-optim-img").remove(); }
			});
			$(".dialog-optim-img").parent().css({position:"fixed"}).end().dialog('open');

			// Si pas d'image on n'affiche pas la dialog
			//if(num == 0) $(".dialog-optim-img").dialog('close');
		}
	}
}



// Fonction pour scroller de class en class
clickScroll = function(from, to) 
{
	var num = 0;		
	$(from).on("click", function()
	{
		// Choisi la ligne à mettre en surbrillance en fonction de celle déjà active
		if($(to+".active").length == 0 || num+1 >= $(to).length) {
			num = 0;
			var $target = $(to).eq(0);
		}
		else var $target = $(to).eq(num);

		num++;

		// + $("#admin-bar").height()
		$root.animate({ scrollTop: (Math.round($target.offset().top) - 100) }, 300, "linear");
	
		$(".active").removeClass("active");
		$target.addClass("active");
	});
}

// Liste les erreurs d'accessibilité dans la page
clean_access_class = false;
access_check = function(file) 
{
	// @todo ajouter une croix de suppression au survol des éléments vides ?
	
	// Init Variable
	access_error = "";

	var num_empty = 0;
	var num_div_text = 0;
	var num_titre = 0;
	var num_titre_prev = 1;
	var num_brbr = 0;
	var num_alt = 0;
	var num_fig = 0;

	// Clean si dialog access déjà presente
	$(".dialog-access").remove(); 


	/**** 
	 * Check les erreurs d'access
	 *****/
	
	// Parcours les éléments du contenu
	$(".editable p, .editable li, .editable div, .editable h1, .editable h2, .editable h3, .editable h4, .editable h5, .editable h6").each(function() 
	{
		var $this = $(this);

		// Elément html vide // $("p:empty")
		if($this.html() == "" || $this.html() == "<br>\n" || $this.html() == "<br>" || $this.html() == "&nbsp") 
		{
			$this.addClass("access_empty");
			++num_empty;
		}

		// DIV avec texte
		if($this[0].nodeName == 'DIV' && $this.text() != '' && !$this.hasClass("highlight") && !$this.hasClass("grid") && !$this.parent().hasClass("grid") )
		{
			$this.addClass("access_div");
			++num_div_text;
		}

		// Vérifie que les titres sont dans l'ordre
		if(['H1','H2','H3','H4','H5','H6'].includes($this[0].nodeName))
		{
			var num_titre_current = $this[0].nodeName.substring(1);

			// Vérifie que le titre est bien à un niveau+1
			if(
				num_titre_prev > 1 
				&& num_titre_prev != num_titre_current 
				&& num_titre_prev < num_titre_current 
				&& (+num_titre_prev+1) != num_titre_current 
			)
			{
				$this.addClass("access_titre");
				++num_titre;
			}
			else num_titre_prev = num_titre_current;
		}		
	});

	// Double <br><br>
	
	$(".editable br").each(function() 
	{
		// @todo supp => version non performante car manipule tout le contenu et pas les noeuds
		//var html = $(this).html();
		//html = html.replace(/(?:<br[^>]*>\s*){2,}/gi, "<br><br class='access_brbr'>");
		//$(this).html(html);

		// Version de recherche des double br, qui manipule uniquement les br dans la dom
		const {nodeName} = this;

		let node = this;

		while(node = node.previousSibling) {
			if(node.nodeType !== Node.TEXT_NODE || node.nodeValue.trim() !== '') break;		
		}

		if(
			node && node !== this && node.nodeName === nodeName && 
			node.nextSibling && node.nextSibling.nodeName == "BR"
			)
			$(node.nextSibling).addClass("access_brbr");
	});
	$(".access_brbr").each(function() { ++num_brbr; });

	// $('br').map(function(){
	// 	if(($next = $(this).next()).is('br')) {
	// 		$next.addClass("access_brbr");
	// 		++num_brbr;
	// 	}
	// });
	
	// Texte alternatif dans une image <img alt="">
	// $(".editable img").each(function() 
	// {
	// 	var $this = $(this);

	// 	if($(this).attr("alt") != "") {
	// 		$this.addClass("access_alt");
	// 		++num_alt;
	// 	}		
	// });

	// Vérifie les textes alternatifs dans les images dans une figure
	// $(".editable figure").each(function() 
	// {
	// 	var $this = $(this);

	// 	console.log($("img", $this).attr("alt") +"**"+$("figcaption", $this).text())

	// 	//$("img", $this).attr("alt") == "" && 
	// 	if($("img", $this).length && $("figcaption", $this).text()) {
	// 		$("img", $this).addClass("access_fig");
	// 		++num_fig;
	// 	}		
	// });



	/****
	 * Construction des affichages d'erreurs
	 *****/

	// Affiche les champs vides
	if(num_empty > 0) 		
	{
		access_error += "<li class='pbt pointer empty_toscroll'><i class='fa fa-attention red mrt' aria-hidden='true'></i><span class='access_empty plt prt'>"+num_empty+"</span> élément"+(num_empty>1?"s":"")+" vide"+(num_empty>1?"s":"")+"</li>";
	}

	// Affiche les div avec texte
	if(num_div_text > 0) 		
	{
		access_error += "<li class='pbt pointer div_text_toscroll'><i class='fa fa-attention red mrt' aria-hidden='true'></i><span class='access_div plt prt'>"+num_div_text+"</span> élément"+(num_div_text>1?"s":"")+" pas dans un paragraphe</li>";
	}

	// Affiche les erreurs d'ordre des titres
	if(num_titre > 0) 		
	{
		access_error += "<li class='pbt pointer titre_toscroll'><i class='fa fa-attention red mrt' aria-hidden='true'></i><span class='access_titre plt prt'>"+num_titre+"</span> titre"+(num_titre>1?"s":"")+" dans le mauvais ordre</li>";
	}

	// Affiche les erreurs de double <br><br>
	if(num_brbr > 0) 		
	{
		access_error += "<li class='pbt pointer brbr_toscroll'><i class='fa fa-attention red mrt' aria-hidden='true'></i><span class='access_brbr plt prt'>"+num_brbr+"</span> double"+(num_brbr>1?"s":"")+" retour"+(num_brbr>1?"s":"")+" à la ligne <i class='fa fa-fw fa-trash'></i></li>";
	}

	// Affiche les avertissements concernant les textes alternatifs sur les images
	// if(num_alt > 0) 		
	// {
	// 	access_error += "<li class='pbt pointer alt_toscroll'><i class='fa fa-attention orange mrt' aria-hidden='true'></i><span class='access_alt plt prt'>"+num_alt+"</span> image"+(num_alt>1?"s":"")+" avec texte alternatif <i class='fa fa-info-circled grey' title=\"Il s'agit bien d'image porteuse d'information et pas d'image décorative ?\nVotre texte est-il pertinent (il reprend les informations textuelles incluses dans l'image) ?\"></i></li>";
	// }
	
	// Affiche les avertissements concernant les textes alternatifs sur les images
	// if(num_fig > 0) 		
	// {
	// 	access_error += "<li class='pbt pointer fig_toscroll'><i class='fa fa-attention orange mrt' aria-hidden='true'></i><span class='access_fig plt prt'>"+num_fig+"</span> la légende est l'image son bien liée ? <i class='fa fa-info-circled grey' title=\"La légende doit commencer par un texte qui reprend idéalement celui du texte alternatif de l'image, ceci afin d'avoir un lien sémantique.\"></i></li>";
	// }


	/****
	 * Affichage des erreurs
	 *****/
	if(access_error)
	{
		// Supprime les anciennes occurrences de la fenêtre
		$(".dialog-access").dialog('close');

		// Dialog des erreur d'access // nw
		$("body").append("<div class='dialog-access' title='"+__("Accessibilité")+"'><ul class='pan unstyled small'>"+ access_error +"</ul></div>");

		// Scroll pour voir les éléments vide
		clickScroll(".empty_toscroll", ".access_empty");
		
		// Scroll pour voir les div avec texte
		clickScroll(".div_text_toscroll", ".access_div");

		// Scroll pour voir les titres dans le mauvais ordre
		clickScroll(".titre_toscroll", ".access_titre");

		// Scroll pour voir les double <br><br>
		clickScroll(".brbr_toscroll", ".access_brbr");

		// Supprime les doubles <br> d'un coup
		$(".brbr_toscroll .fa-trash").on("click", function(){
			if(confirm("Supprimer tous les doubles retours à la ligne ?")){
				$(".access_brbr").remove();
				$(".brbr_toscroll").remove();
			}
		});

		// Scroll pour voir les images avec alt
		//clickScroll(".alt_toscroll", ".access_alt");

		// Scroll pour voir les images avec légende
		//clickScroll(".fig_toscroll", ".access_fig");


		// Dialog en layer
		$(".dialog-access").dialog({
			autoOpen: false,
			width: 'auto',
			maxHeight: 500,
			position: { my: "right top", at: "right-10 bottom+10", of: $("#admin-bar") },
			show: function() {$(this).fadeIn(300);},
			close: function() { $(".dialog-access").remove(); }
		});
		$(".dialog-access").parent().css({position:"fixed"}).end().dialog('open');


		// Avant la sauvegarde supprime les class de surbrillances des erreurs
		if(!clean_access_class)
		{ 
			clean_access_class = true; 
			before_data.push(function(){
				$(".editable .active").removeClass("active");
				$(".access_empty").removeClass("access_empty");
				$(".access_div").removeClass("access_div");
				$(".access_titre").removeClass("access_titre");
				$(".access_brbr").removeClass("access_brbr");
				//$(".access_alt").removeClass("access_alt");
				//$(".access_fig").removeClass("access_fig");
			});
		}


		// Si dialog sur l'accessibilité et check img on place la dialog des images en dessous // left-5
		if($(".dialog-optim-img").length >= 1) 
			$(".dialog-optim-img").dialog({
				position: { my: 'right+5 top', at: 'right bottom+15', of: (".dialog-access") }
			});

		// Si pas d'erreur d'access on n'affiche pas la dialog
		//if(num == 0) $(".dialog-access").dialog('close');
	}
}



/*
* Pour plus d'informations sur ecoindex : 
* http://www.ecoindex.fr/quest-ce-que-ecoindex/
*
*  Copyright (C) 2019  didierfred@gmail.com 
*   *
*  This program is free software: you can redistribute it and/or modify
*   *  it under the terms of the GNU Affero General Public License as published
*  by the Free Software Foundation, either version 3 of the License, or
*   *  (at your option) any later version.
*  This program is distributed in the hope that it will be useful,
*   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   *  GNU Affero General Public License for more details.
*  You should have received a copy of the GNU Affero General Public License
*   *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
quantiles_dom = [0, 47, 75, 159, 233, 298, 358, 417, 476, 537, 603, 674, 753, 843, 949, 1076, 1237, 1459, 1801, 2479, 594601];
quantiles_req = [0, 2, 15, 25, 34, 42, 49, 56, 63, 70, 78, 86, 95, 105, 117, 130, 147, 170, 205, 281, 3920];
quantiles_size = [0, 1.37, 144.7, 319.53, 479.46, 631.97, 783.38, 937.91, 1098.62, 1265.47, 1448.32, 1648.27, 1876.08, 2142.06, 2465.37, 2866.31, 3401.59, 4155.73, 5400.08, 8037.54, 223212.26];

/**
Calcul ecoIndex based on formula from web site www.ecoindex.fr
**/
function computeEcoIndex(dom,req,size)
{

	const q_dom= computeQuantile(quantiles_dom,dom);
	const q_req= computeQuantile(quantiles_req,req);
	const q_size= computeQuantile(quantiles_size,size);


	return 100 - 5 * (3*q_dom + 2*q_req + q_size)/6;
}

function computeQuantile(quantiles,value)
{
	for (i=1;i<quantiles.length;i++)
	{
		if (value<quantiles[i]) return (i + (value-quantiles[i-1])/(quantiles[i] -quantiles[i-1]));
	}
	return quantiles.length;
}

function getEcoIndexGrade(ecoIndex)
{
	if (ecoIndex > 80) return "A";//75
	if (ecoIndex > 70) return "B";//65
	if (ecoIndex > 55) return "C";//50
	if (ecoIndex > 40) return "D";//35
	if (ecoIndex > 25) return "E";//20
	if (ecoIndex > 10) return "F";//5
	return "G";
}

function computeGreenhouseGasesEmissionfromEcoIndex(ecoIndex)
{
	return (2 + 2 * (50 - ecoIndex) / 100).toFixed(2);
}
function computeWaterConsumptionfromEcoIndex(ecoIndex)
{
	return (3 + 3 * (50 - ecoIndex) / 100).toFixed(2);
}


// Donne le ecoindex
ecoindex = function(dom, resources)
{
	// Mesure le nombre de REQUETTE
	var size = req = error = 0;

	// C'est une iframe du coup regarder si favicon dans les metas
	var link_favicon = $('link[rel~="icon"]').prop('href');
	if(link_favicon) resources.push({name: link_favicon, transferSize: 0});
	//else resources.push({name:$(location).attr('origin')+'/favicon.ico', transferSize: 0});


	// Parcours les ressources pour le nombre de requette et leur poids
	resources.forEach(function(resource) 
	{
		// Nombre de ressource
		req++;

	    // Poids du fichier en octets typeof resource.transferSize !== 'undefined' && 
	    var size_file = 0;
	    if(resource.transferSize == 0)// Si la boucle n'arrive pas à lire le poids du fichier
	    {
		    /*var request = new XMLHttpRequest();
		    request.open("HEAD", resource.name, false);
		    request.send(null);
			//request.getResponseHeader('content-length');
			/Content\-Length\s*:\s*(\d+)/i.exec(request.getAllResponseHeaders());
		    size_file = parseInt(RegExp.$1);*/
		    // @todo : Gerer les cas ou il n'y a pas de content-length

		    error++;		   
	    }
	    else size_file = resource.transferSize;

	    // Poids en Ko
	    size_file = Math.round(size_file / 1000);

	    // Poids total de fichier
	    size = size + size_file;

	    //console.log(resource);
	    //console.log(req+' : '+size_file+'Ko | '+resource.transferSize+' | '+resource.initiatorType+' | '+resource.name);
	});


	// Résultat
	var p100error = error * 100 / req;
	var ecoIndex = computeEcoIndex(dom, req, size);
	var EcoIndexGrade = getEcoIndexGrade(ecoIndex)
	var ges = computeGreenhouseGasesEmissionfromEcoIndex(ecoIndex)
	var eau = computeWaterConsumptionfromEcoIndex(ecoIndex)

	// Log
	//console.log("ecoIndex: " + ecoIndex);
	//console.log("EcoIndexGrade: " + EcoIndexGrade);
	//console.log("ges: " + ges);
	//console.log("eau: " + eau);
	//console.log("dom: " + dom);
	//console.log("req: " + req);
	//console.log("size: " + size);


	// Affichage dans la barre d'admin
	var ecotitle = 'ecoIndex: '+ ecoIndex.toFixed(2) + (p100error>0?' (*'+Math.round(p100error)+'% d\'erreur)':'') +' | GES: '+ges+' gCO2e | eau: '+eau+' cl | Nombre de requêtes: '+req+' | Taille de la page: '+size+' Ko | Taille du DOM: '+dom;

	// Ajout de la note dans la barre d'admin
	if(!$("#ecoindex").length)
	{
		$("#admin-bar").append('<a href="http://www.ecoindex.fr/quest-ce-que-ecoindex/"  id="ecoindex" class="fr mat mrs small none" target="_blank" title="'+ecotitle+'">ecoIndex<span class="'+EcoIndexGrade+'">'+EcoIndexGrade + (p100error>0?'*':'') +'</span></a>');
		$("#ecoindex").fadeIn();
	}
	else
	{
		$("#ecoindex span").html(EcoIndexGrade).removeClass("A B C D E F").addClass(EcoIndexGrade);
		$("#ecoindex").attr("title", ecotitle);
	}

	// Supprime l'iframe
	$("#iframe_ecoindex").remove();

}



// Désactive l'edition
unlucide = function()
{
	lucide = false;

	$("body").removeClass("lucide");

	$("#admin-bar").slideUp("400", function(){ $(this).remove(); });
	
	$("#txt-tool").remove();
	$("#add-nav").remove();
	$("[contenteditable='true']").attr("contenteditable","false");
}


// Vérifie que le contenu est sauvegardé en cas d'action de fermeture ou autres
$(window).on("beforeunload", function(){
	if(typeof dev == 'undefined' && $("#admin-bar button.to-save").length || $("#save i.fa-spin").length) return __("The changes are not saved");
});



/************** ONLOAD **************/
$(function()
{						
	//@todo: ajouter le choix de la langue de la page en cours
	
	lucide = true;
	memo_node = null;
	autocompleteOpen = false;

	// Ajout de la class pour dire que l'on est en mode admin
	$("body").addClass("lucide");


	/************** ADMINBAR **************/

	// Ajout des variables dans les inputs (pour le problème de double cote ")
	$("#admin-bar #title").val(title);

	if($("meta[name=description]").last().attr("content") != undefined) 
		description = $('meta[name=description]').last().attr("content");
	else
		description = "";

	$("#admin-bar #description").val(description);


	if(/noindex/i.test($('body').data("robots"))) $("#admin-bar #noindex").prop("checked", true);
	if(/nofollow/i.test($('body').data("robots"))) $("#admin-bar #nofollow").prop("checked", true);


	$("#admin-bar #permalink").val(permalink);
	$("#admin-bar #type").val(type);
	$("#admin-bar #tpl").val(tpl);
	$("#admin-bar #date-insert").val($("meta[property='article:published_time']").last().attr("content").slice(0, 19).replace('T', ' ')).datepicker({dateFormat: 'yy-mm-dd 00:00:00', firstDay: 1});


	// Checkbox homepage si c'est une page // Plus afficher pour éviter les mauvaises manipulation
	//if(type == "page") $("#admin-bar #ispage").show();

	// Etat de la checkbox homepage onready
	if($("#admin-bar #permalink").val() == "index") $("#admin-bar #homepage").prop("checked", true);
	
	// Si on change le permalink on verif que c'est 'home'
	$("#admin-bar #permalink").keyup(function() {
		if($(this).val() == "index") $("#admin-bar #homepage").prop("checked", true);
		else $("#admin-bar #homepage").prop("checked", false);
	});

	// Changement au click de la checkbox homepage
	$("#admin-bar #homepage").change(function() {
		if(this.checked) $("#admin-bar #permalink").val("index");
		else refresh_permalink("#admin-bar");
		tosave();// A sauvegarder
	});

	// Click refresh permalink
	$("#admin-bar #refresh-permalink").on("click", function() {
		refresh_permalink("#admin-bar");
	});

	// Dossier spécifique média pour l'image pour og:image
	if($("#visuel").data('dir')) $("#admin-bar #og-image").data('dir', $("#visuel").data('dir'));
	else if($("#alaune").data('dir')) $("#admin-bar #og-image").data('dir', $("#alaune").data('dir'));

	// On récupère og:image des meta
	if($("meta[property='og:image']").last().attr("content") != undefined) 
	{
		// Bind l'image
		$("#admin-bar #og-image img").attr("src", $("meta[property='og:image']").last().attr("content"));

		// Option de suppression de l'image
		$("#admin-bar #og-image").after("<a href='javascript:void(0)' onclick=\"$('#admin-bar #og-image img').attr('src','');$(this).remove();\"><i class='fa fa-cancel absolute' title='"+ __("Remove") +"'></i></a>");
	}

	// Ajout de l'état de la page
	if(state == "active") $("#admin-bar #state-content").prop("checked", true);
	else $("#admin-bar #state-content").prop("checked", false);

	// Ouverture de l'édition du title si en mode responsive
	$("#meta-responsive i").on("click",	function() {
		$("#meta").addClass("tooltip slide-left fire pat").css({"position": "absolute", "top": $("#admin-bar").height()}).fadeToggle().attr('style', function(i,s) { return (s || '') + 'display: block !important;' });		
	});


	/************** CONTENTEDITABLE **************/

	// spellcheck="false" wrap="off" autofocus placeholder="Enter something ..."
	
	// Pour corriger le drag&drop de texte dans firefox span > div
	$(".editable").replaceWith(function () 
	{ 
		// Pour corriger les div qui ne prennent pas toutes la largeur a cause des img en float
		var style = null;
		if($(this).parent().is("article") && $(this).parent().width()>0) 
			style = "width: "+$(this).parent().width()+"px;";//if($(this).parent().children().length <= 1)

		// Clean la dom
		return $("<"+ $(this)[0].tagName.toLowerCase() +"/>", { 
			class: $(this).attr("class"),
			id: this.id,
			html: this.innerHTML,
			style: style,
			"data-dir": $(this).data("dir"),
			"data-builder": $(this).data("builder"),
			placeholder: $(this).attr("placeholder")
		});
	});


	// Si readonly
	$(".editable.readonly").attr("contenteditable", false);

	// Si champ numerique on ne garde que les chiffre et les points
	$(".editable.number").on("keypress", function(event){//input keyup keydown change
		key = event.keyCode || event.charCode;

		// <- -> backspace supp && ., && [0-9]
		if(
			(!/^(37|39|8|46)$/.test(event.keyCode) && !/^(46|44)$/.test(event.charCode) && !(key >= 48 && key <= 57))// Si pas point/virgule et pas chiffre
			||
			(/^(46|44)$/.test(event.charCode) && /[.,]/.test(this.innerHTML))// Si point/virgule si déjà présent
		) 
		event.preventDefault();
	});


	// Place les contenus au-dessus pour les rendre éditables à coup sur
	$(".editable").parent().css("z-index", "1");//10 avant => bug avec datepicker

	// Pour pouvoir éditer un contenu dans un label
	$(".editable").parent("label").attr("for","");


	// Rends communiquant les champs titles dans l'édition et l'admin-bar
	$("#admin-bar #title").on("keyup", function(event){
		$(".editable#title").html($(this).val());
	});
	$(".editable#title").on("keyup", function(event){
		$("#admin-bar #title").val($(this).text());
	});


	/************** MENU NAV **************/
	
	// Si menu on inject les fonctionnalitées
	if($("header nav > ul:not(.exclude)").length)
	{
		// Bloc d'option pour le menu de navigation  class='black'
		addnav = "<div id='add-nav'>";
			addnav+= "<div class='zone bt' title='"+ __("Edit menu") +"'><i class='fa fa-fw fa-pencil bigger vam'></i></div>";
			addnav+= "<div class='tooltip none pat'>";
				addnav+= "<i class='fa fa-cancel grey o50'></i>";
				addnav+= "<ul class='block unstyled plm man tl'>";
					addnav+= "<li class='add-empty'><div class='dragger'></div><a href='#'>"+__("Empty element")+"</a><i onclick='$(this).parent().appendTo(\"#add-nav ul\");' class='fa fa-cancel red' title='"+ __('Remove') +"'></i></li>";
				addnav+= "</ul>";
			addnav+= "</div>";	
		addnav+= "</div>";	
		$("header nav > ul:not(.exclude)").after(addnav);

		// Positionne le menu
		// Barre admin + position top du menu + marge du menu - hauteur du bt edit menu
		var top_bt_menu = $("#admin-bar").outerHeight() + $("header nav > ul:not(.exclude)").offset().top + parseInt($("header nav > ul:not(.exclude)").css("marginTop").replace('px', '')) - ($("#add-nav").outerHeight());
		$("#add-nav").css("top", top_bt_menu + "px");
		

		// Déplace un élément du menu add vers le menu courant au click sur le +
		hover_add_nav = false;	
		$("#add-nav").on({
			"click.dragger": function(event) {
				event.preventDefault();  
				//event.stopPropagation();

				// Si on est bien sur un élément ajoutable
				if(event.target.className == "dragger")
				{
					if($(event.target).parent().hasClass("add-empty"))
						$("header nav ul:not(.exclude):first").append($(event.target).parent().clone());// Copie
					else
						$($(event.target).parent()).appendTo("header nav ul:not(.exclude):first");// Déplace
					
					// Rends editable les éléments du menu (pointage sur A)
					$("header nav ul:not(.exclude):first li a").attr("contenteditable","true").addClass("editable");
					editable_event();

					tosave();// A sauvegarder
						
					// Désactive les liens dans le menu d'ajout
					$("#add-nav ul:not(.exclude) a").on("click", function() { return false; });
				}
			},
			// On check si on est sur le menu d'ajout de page au menu
			"mouseenter": function(event) { hover_add_nav = true; },
			"mouseleave": function(event) {	hover_add_nav = false; }
		});
		
		// Drag & Drop des éléments du menu principal
		$("header nav ul:not(.exclude):first").sortable({
			connectWith: "header nav ul:not(.exclude), #add-nav .zone",
			handle: ".dragger",
			axis: "x",
			start: function(event) {
				$("#add-nav").addClass("del");
				$("#add-nav .zone i").removeClass("fa-plus").addClass("fa-trash");

				$(".editable").off();//$("body").off(".editable");		
				$("header nav ul:not(.exclude):first li a").attr("contenteditable","false").removeClass("editable");
			},
			stop: function(event, ui) {
				// Si on drop l'element dans la zone de suppression mais pas dans le ul liste autre page
				if($(event.toElement).closest("#add-nav").length && !$(event.toElement).parent("#add-nav .tooltip").length)
					$(ui.item).remove();// On le supprime de la nav

				$("#add-nav").removeClass("del");
				$("#add-nav .zone i").removeClass("fa-trash").addClass("fa-plus");
				
				// Rends editable les éléments du menu
				$("header nav ul:not(.exclude):first li a").attr("contenteditable","true").addClass("editable");
				editable_event();

				tosave();// A sauvegarder
				
				// désactive les liens dans le menu d'ajout
				$("#add-nav ul:not(.exclude) a").on("click", function() { return false; });
			}
		});

		// Si on est en mode burger on active le tri verticalement
		$(".burger, .sortable-y").on("click", function() {
			$("header nav ul:not(.exclude):first").sortable("option", "axis", "y");
		});

		// Si on demande à ce que le menu soit triable verticalement
		if($(".sortable-y").length) $("header nav ul:not(.exclude):first").sortable("option", "axis", "y");
		
		// Rend clonable uniquement le bloc vide
		$(".add-empty").draggable({
			connectToSortable: "header nav ul:not(.exclude):first",
			helper: "clone",
			revert: "invalid"
	    });


		// Affichage du bouton pour ouvrir le menu d'ajout
		on_header = false;
		add_page_bt = false;		
		$("header").on({
			"mouseover": function(event) {//mouseenter

				on_header = true;// On est sur le header avec la souris

				if(!add_page_bt)
				{
					// Affichage du bouton pour l'ouverture du menu d'ajout
					$("#add-nav").css({opacity: 0, display: 'inline-block'}).animate({opacity: 0.8}, 300);

					add_page_bt = true;// Bouton pour ouvrir le layer de liste de page, visible
				}
				else if($("#add-nav").css("display") != "block")// Si pas affiché on l'affiche
					$("#add-nav").css({opacity: 0, display: 'inline-block'}).animate({opacity: 0.8}, 300);
			},
			// Si on sort du header, on check si on est sur le menu d'ajout de page avant de le fermer
			"mouseleave": function(event) {
				on_header = false;
				setTimeout(function() { 
					if(!add_page_list && !hover_add_nav && !on_header) $("#add-nav").fadeOut("fast");
				}, 1000);			
			}
		});

		// Ouverture de la liste des pages disponibles absente du menu au click sur le +
	    add_page_list = false;
		$("#add-nav .zone, #add-nav .fa-cancel").on({
			"click": function(event) {
				event.preventDefault();  
				//event.stopPropagation();

				// Ouvre le menu d'ajout
				// Cherche dans la base les pages manquantes
				if(!add_page_list)
				{
					// Rends le menu de navigation éditable
					// Cible le A pour eviter les bug de selection de navigateur qui sortent du lien
					$("header nav ul:not(.exclude):first li a").attr("contenteditable","true").addClass("editable");

					// Pour la toolbox sur les éléments du menu
					editable_event();

					// Ajout d'une zone de drag pour chaque élément
					$("header nav ul:not(.exclude):first li").prepend("<div class='dragger'></div>");


					// Liste les pages déjà dans le menu
					var menu = {};
					$(document).find("header nav ul:not(.exclude) a").each(function(index) { menu[index] = $(this).attr('href'); });

					$.ajax({
						type: "POST",
						url: path+"api/ajax.admin.php?mode=add-nav",
						data: {
							"menu" : menu,
							"nonce": $("#nonce").val()
						},
						success: function(html){ 
							$("#add-nav .tooltip ul").append(html);		
							
							// Pour éviter de relancer l'ajax
							add_page_list = true;

							// Rends draggable les pages manquantes du menu
							$("#add-nav .tooltip ul").sortable({
								connectWith: "header nav ul:not(.exclude)",
								start: function() {
									$(".editable").off();//$("body").off(".editable");
									$("header nav ul:not(.exclude):first li a").attr("contenteditable","false").removeClass("editable");
								},
								stop: function() {
									$("header nav ul:not(.exclude):first li a").attr("contenteditable","true").addClass("editable");
									editable_event();
									tosave();// A sauvegarder
								}
							});
								
							// désactive les liens
							$("#add-nav ul a").on("click", function() { return false; });

							// Affichage de la liste
							$("#add-nav .tooltip").show();//slideDown

							// Changement de la class zone +
							$("#add-nav").addClass("open");

							// Ajoute la croix de suppression // $(this).parent().remove()
							$("nav ul:not(.exclude):first li").append("<i onclick='$(this).parent().appendTo(\"#add-nav ul\");' class='fa fa-cancel red' title='"+ __("Remove") +"'></i>");
						}
					});
				}
				else// Ferme le menu d'ajout
				{
					add_page_list = false;

					// Affichage de la liste
					$("#add-nav .tooltip").hide();//slideUp

					// Changement de la class zone +
					$("#add-nav").removeClass("open");

					// Supprime la croix de suppression
					$("nav ul:not(.exclude):first .fa-cancel").remove();

					// Supprime l'edition des élément du menu
					$("header nav ul:not(.exclude):first li a").attr("contenteditable","false").removeClass("editable").off();

					// Supprime la zone de drag pour chaque élément
					$("header nav ul:not(.exclude):first li .dragger").remove();
				}
			}
		});

	}


	/************** TOOLBOX **************/

	// Barre d'outils de mise en forme : toolbox
	toolbox = "<ul id='txt-tool' class='toolbox' role='toolbar'>";

		if(typeof toolbox_h2 != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool('h2')\" id='h2' title=\""+__("Title")+" H2"+"\"><i class='fa fa-fw fa-header'></i><span class='minus'>2</span></button></li>";

		if(typeof toolbox_h3 != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool('h3')\" id='h3' title=\""+__("Title")+" H3"+"\"><i class='fa fa-fw fa-header'></i><span class='minus'>3</span></button></li>";

		if(typeof toolbox_h4 != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool('h4')\" id='h4' title=\""+__("Title")+" H4"+"\"><i class='fa fa-fw fa-header'></i><span class='minus'>4</span></button></li>";

		if(typeof toolbox_h5 != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool('h5')\" id='h5' title=\""+__("Title")+" H5"+"\"><i class='fa fa-fw fa-header'></i><span class='minus'>5</span></button></li>";

		if(typeof toolbox_h6 != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool('h6')\" id='h6' title=\""+__("Title")+" H6"+"\"><i class='fa fa-fw fa-header'></i><span class='minus'>6</span></button></li>";

		if(typeof toolbox_bold != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('bold')\" title=\""+__("Bold")+"\"><i class='fa fa-fw fa-bold'></i></button></li>";

		if(typeof toolbox_italic != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('italic')\" title=\""+__("Italic")+"\"><i class='fa fa-fw fa-italic'></i></button></li>";

		if(typeof toolbox_underline != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('underline')\" title=\""+__("Underline")+"\"><i class='fa fa-fw fa-underline'></i></button></li>";
		
		if(typeof toolbox_superscript != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('superscript')\" title=\""+__("Superscript")+"\"><i class='fa fa-fw fa-superscript'></i></button></li>";
				
		if(typeof toolbox_fontSize != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('fontSize', '2')\" title=\""+__("R\u00e9duire la taille du texte")+"\"><i class='fa fa-fw fa-resize-small'></i></button></li>";
		
		if(typeof toolbox_color != 'undefined' && nbcolor >= 1)
		{
			toolbox+= "<li><button onclick=\"$('#txt-tool #color-option').toggle();$('.toolbox #color-option button[class^=color-]').removeClass('checked');\" title=\""+__("Add Color")+"\" class='color-option'><span class='color-1'>■</span><span class='color-2'>■</span><br><span class='color-3'>■</span><span class='color-4'>■</span></button></li>";

			toolbox+= "<li id='color-option' class='option'>";

				for(i=1; i<=nbcolor ;i++) 
					toolbox+= "<button onclick=\"color_text('color-"+i+"')\" class='color-"+i+"'>■</button>";

			toolbox+= "</li>";
		}

		if(typeof toolbox_p != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool('p')\" id='p' title=\""+__("Paragraph")+"\"><i class='fa fa-fw fa-paragraph'></i></button></li>";

		if(typeof toolbox_blockquote != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool('blockquote')\" id='blockquote' title=\""+__("Blockquote")+"\"><i class='fa fa-fw fa-quote-left smaller'></i><i class='fa fa-fw fa-quote-right smaller'></i></button></button></li>";

		//changer la fonction color_text pour la rendre plus générique et fonctionner aussi avec <q>
		if(typeof toolbox_q != 'undefined') 
			toolbox+= "<li><button onclick=\"html_tool_inline('q')\" id='q' title=\""+__("Quote line")+"\" class=\"\"><i class='fa fa-fw fa-quote-left small'></i></li>";

		if(typeof toolbox_highlight != 'undefined') //highlight() class_tool('highlight')
			toolbox+= "<li><button onclick=\"highlight()\" id='tool-highlight' title=\""+__("Highlight")+"\"><i class='fa fa-fw fa-info-circled'></i></button></li>";

		if(typeof toolbox_grid != 'undefined') 
		{
			toolbox+= "<li><button onclick=\"$('#txt-tool #grid-option').toggle();\" id='tool-grid' title=\""+__("Grid")+"\"><i class='fa fa-fw fa-th-large'></i></button></li>";

			toolbox+= "<li id='grid-option' class='option'>";

				for(i=2; i<=4 ;i++)
					toolbox+= "<button onclick=\"grid('"+i+"')\" class='' title=\""+i+" "+__("Column")+(i>1?'s':'')+"\">"+i+"</button>";

			toolbox+= "</li>";
		}

		if(typeof toolbox_insertUnorderedList != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('insertUnorderedList')\" id='insertUnorderedList' title=\""+__("Insert list")+"\"><i class='fa fa-fw fa-list'></i></button></li>";

		if(typeof toolbox_justifyLeft != 'undefined')// justifyLeft
			toolbox+= "<li><button onclick=\"class_tool('tl')\" id='tool-tl' title=\""+__("Justify Left")+"\"><i class='fa fa-fw fa-align-left'></i></button></li>";

		if(typeof toolbox_justifyCenter != 'undefined')// justifyCenter
			toolbox+= "<li><button onclick=\"class_tool('tc')\" id='tool-tc' title=\""+__("Justify Center")+"\"><i class='fa fa-fw fa-align-center'></i></button></li>";

		if(typeof toolbox_justifyRight != 'undefined')// justifyRight
			toolbox+= "<li><button onclick=\"class_tool('tr')\" id='tool-tr' title=\""+__("Justify Right")+"\"><i class='fa fa-fw fa-align-right'></i></button></li>";

		if(typeof toolbox_justifyFull != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('justifyFull')\" id='align-justify' title=\""+__("Justify")+"\"><i class='fa fa-fw fa-align-justify'></i></button></li>";

		if(typeof toolbox_InsertHorizontalRule != 'undefined') 
			toolbox+= "<li><button onclick=\"exec_tool('InsertHorizontalRule')\" title=\""+__("Ajoute une barre de s\u00e9paration")+"\"><i class='fa fa-fw fa-resize-horizontal'></i></button></li>";

		if(typeof toolbox_viewsource != 'undefined') 
			toolbox+= "<li><button onclick=\"view_source(memo_focus)\" id='view-source' title=\""+__("See the source code")+"\"><i class='fa fa-fw fa-code'></i></button></li>";

		if(typeof toolbox_icon != 'undefined') 
			toolbox+= "<li><button onclick=\"dialog('icon', memo_focus)\" title=\""+__("Icon Library")+"\"><i class='fa fa-fw fa-flag'></i></button></li>";

		if(typeof toolbox_videoLink != 'undefined') { videoLink = true; toolbox_video = true; } else videoLink = false;
		if(typeof toolbox_video != 'undefined') 
		{
			toolbox+= "<li><button onclick=\"$('#txt-tool #video-option').show(); $('#txt-tool #video-option #video').select();\" title=\""+__("Add Video")+"\"><i class='fa fa-fw fa-video'></i></button></li>";

			toolbox+= "<li id='video-option' class='option'>";

				toolbox+= "<input type='text' id='video' placeholder='https://youtu.be/***' title=\""+ __("Video") +"\" class='w150p small'>";

				toolbox+= "<button onclick=\"video("+videoLink+")\" class='small plt prt'><span>"+ __("Add Video") +"</span><i class='fa fa-fw fa-plus'></i></button>";

			toolbox+= "</li>";
		}

		if(typeof toolbox_media != 'undefined') 
			toolbox+= "<li><button onclick=\"media(memo_focus, 'intext')\" title=\""+__("Media Library")+"\"><i class='fa fa-fw fa-picture'></i></button></li>";

		//toolbox+= "<li><button onclick=\"exec_tool('unlink')\"><i class='fa fa-fw fa-chain-broken'></i></button></li>";

		if(typeof toolbox_lang != 'undefined') 
		{
			toolbox+= "<li><button onclick=\"lang_option(); $('#txt-tool #lang-option #lang').select();\" title=\""+__("Add Language")+"\"><i class='fa fa-fw fa-language '></i></button></li>";

			toolbox+= "<li id='lang-option' class='option'>";
				toolbox+= "<input type='text' id='lang' placeholder=\""+ __("Language") +"\" title=\""+ __("Language") +"\" class='w150p small'>";
				toolbox+= "<button onclick=\"edit_lang()\" class='small plt prt'><span>"+ __("Add Language") +"</span><i class='fa fa-fw fa-plus'></i></button>";
			toolbox+= "</li>";
		}

		if(typeof toolbox_anchor != 'undefined') 
		{
			toolbox+= "<li><button onclick=\"anchor_option(); $('#txt-tool #anchor-option #anchor').select();\" title=\""+__("Add Anchor")+"\"><i class='fa fa-fw fa-hashtag grey'></i></button></li>";

			toolbox+= "<li id='anchor-option' class='option'>";
				toolbox+= "<input type='text' id='anchor' placeholder=\""+ __("Anchor") +"\" title=\""+ __("Anchor") +"\" class='w150p small'>";
				toolbox+= "<button onclick=\"edit_anchor()\" class='small plt prt'><span>"+ __("Add Anchor") +"</span><i class='fa fa-fw fa-plus'></i></button>";
			toolbox+= "</li>";
		}

		if(typeof toolbox_link != 'undefined') 
		{
			toolbox+= "<li><button onclick=\"link_option(); $('#txt-tool #link-option #link').select();\" title=\""+__("Add Link")+"\"><i class='fa fa-fw fa-link'></i></button></li>";

			toolbox+= "<li id='link-option' class='option'>";

				toolbox+= "<input type='text' id='link-text' placeholder=\""+ __("Link text") +"\" title=\""+ __("Link text") +"\" class='w150p small none'>";

				toolbox+= "<input type='text' id='link' placeholder='http://' title=\""+ __("Link") +" URL\" class='w150p small'>";

				if(typeof toolbox_bt != 'undefined') toolbox+= "<a href=\"javascript:class_bt();void(0);\" title=\""+ __("Apparence d'un bouton") +"\" id='class-bt' class='o50 ho1'><i class='fa fa-login mlt mrt vam'></i></a>";
				
				toolbox+= "<a href=\"javascript:target_blank();void(0);\" title=\""+ __("Open link in new window") +"\" id='target-blank' class='o50 ho1'><i class='fa fa-link-ext mlt mrt vam'></i></a>";

				toolbox+= "<button onclick=\"exec_tool('CreateLink', $('#txt-tool .option #link').val())\" class='small plt prt'><span>"+ __("Add Link") +"</span><i class='fa fa-fw fa-plus'></i></button>";//link()

			toolbox+= "</li>";
		}

	toolbox+= "</ul>";
	
	// Init la toolbox
	$("body").append(toolbox);

	
	// Fonction de positionnement de la toolbox
	toolbox_position = function(top, left, position) {		
		// Valeur par défaut de "position"
		if(typeof position === 'undefined') var position = "absolute";

		// Posionnement
		$("#txt-tool").css({
			top: top + "px",
			left: left + "px",
			position: position
		});
	}	

	// Focus dans un élément vide
	empty_focus = function(selector) {
		if(dev) console.log("empty_focus");
		range = document.createRange();
		range.selectNodeContents(selector);
		range.collapse(false);
		memo_selection = window.getSelection();
		memo_selection.removeAllRanges();
		memo_selection.addRange(range);		
	}

	// @todo supp => instable
	// Nétoie un champ éditable des <br> <p> <div> vide
	clean_editable = function(memo_focus) {
		var clean = ['<br>', '<p><br></p>', '<div><br></div>'];
		if($.inArray($(memo_focus).html(), clean) != -1) {		
			if(dev) console.log("clean_editable");	
			$(memo_focus).html('');
		}
	}

	// Pour pouvoir sortir du figcaption au retour à la ligne
	clean_figcaption = function(selector) {
		if(selector.prop("tagName") == 'FIGCAPTION') {
			if(dev) console.log("clean_figcaption");

			// Ajout du <p> après de figure
			selector.parent("figure").after(p = $("<p></p>"));

			// Si un figcatption est créé en plus on le supprime (chrome)
			if(selector.prev().prop("tagName") == 'FIGCAPTION' && selector.prop("tagName") == 'FIGCAPTION')
			selector.remove();

			// Focus dans le nouveau <p> après la figure
			empty_focus(p[0]);
		}
	}

	// Pour pouvoir sortir du blockquote au retour à la ligne
	clean_blockquote = function(selector) {
		if(selector.prop("tagName") == 'BLOCKQUOTE') {
			if(dev) console.log("clean_blockquote");

			// Remplace le blockquote par un <p> avec le contenu
			selector.replaceWith(p = $("<p>"+selector.html()+"</p>"));

			// Focus dans le nouveau <p> après le blockquote
			empty_focus(p[0]);
		}
	}

	// Si l'élément parent à la class ça ne le duplique pas pour le nouvelle élément, on sort du bloc (highlight|grid)
	clean_return = function(theClass, selector) {		
		if(selector.closest(theClass).length) {
			if(dev) console.log("clean_return");
			
			// Si l'élément précédent est vide on le supprime et on focus dans un nouveau <p> après le bloc avec la class
			if(selector.prev().html() == "" || selector.prev().html() == '<br class="nobr">')
			{
				selector.closest(theClass).after(p = $("<p>"+selector.html()+"</p>"));// Créer un <p> après le bloc avec la class 

				selector.prev().remove();// Supprime
				selector.remove();// Supprime

				empty_focus(p[0]);// Focus dans le nouveau <p> après le bloc avec la class
			}

			//selector.closest(theClass)[0].nextElementSibling
			//selector.removeAttr(theClass);
		}
	}

	// @todo supp => pas utiliser car fait lors du save pour plus de stabilité
	// Si juste un <p> ou <div> avec un <br> => on remplace par un <div> vide avec une marge basse, non vocalisé
	clean_br = function(selector) {
		if(selector.html() == "<br>" || selector.html() == "") {
			if(dev) console.log("clean_br");
			selector.replaceWith($('<div class="pbp"></div>'));
		}
	}

	// @todo supp => instable
	// Supprime les <br> en fin de <p> => pour éviter que le sigle du paragraphe ne soit après la ligne courante
	clean_last_br = function(selector) {
		if(selector.html() == "<br>") {
			if(dev) console.log("clean_last_br");
			selector.html("");
		}
	}

	// Transforme les <div> en <p>
	clean_div = function(selector) {
		// Div, pas une grille
		if(selector.prop("tagName") == "DIV" && !selector.hasClass("grid"))
		{
			if(dev) console.log("clean_div");

			var sel = window.getSelection();// Position du curseur (caret)
			var memo_offset = sel.focusOffset;// Numéro de la position

			// Si on est sur une ligne sans HTML autour on ajoute un <p>
			if(selector.hasClass("editable"))
			{
				if(sel.anchorNode.nodeValue)
				{
					var p = document.createElement("p");// créer un <p>
					p.innerHTML = sel.anchorNode.nodeValue;// ajoute le contenu au nouveau <p>
					sel.anchorNode.parentNode.insertBefore(p, sel.anchorNode.nextSibling);// ajoute le nouveau p après le contenu en cours
					sel.anchorNode.nodeValue = '';// supp le contenu en cours
				}
			}
			else
			{
				// Remplace par un <p>
				selector.replaceWith(p = $('<p>' + selector.html() + '</p>'));
				p = p[0];
			}

			// Repositionne le curseur
			setTimeout(function () { 
				if(p)
				{
					if(memo_offset == 0) {
						if(dev) console.log("empty_focus");
						empty_focus(p);
					}
					else {
						if(dev) console.log("collapse");
						window.getSelection().collapse(p.firstChild, memo_offset);
					}
				}
			}, 0);
			
		}
	}

	// Si contenu vide on met un <p>, seulement pour les div ... pas pour les h1 ...
	init_paragraph = function(memo_focus)
	{
		// Nétoie le champ
		//clean_editable(memo_focus);

		if(/DIV|ARTICLE|section/.test($(memo_focus).prop("tagName")) && $(memo_focus).html() == "")
		{
			if(dev) console.log("init_paragraph");

			// Ajout du pragraphe
			$(memo_focus).html("<p></p>");// append html

			// Pour bug Firefox on positionne le curseur bien à la fin dans le <p>
			empty_focus($('p', memo_focus)[0]);

			// Ajout du placeholder|id sur le <p> vide
			$("style:first").append('#'+$(memo_focus).attr("id")+' p:empty:before {content: "'+($(memo_focus).attr("placeholder") || $(memo_focus).attr("id"))+'";}');
		}
	}

	// Action sur les champs éditables
	editable_event = function()
	{	
		// Rends les textes éditables
		$(".editable").attr("contenteditable","true");

		// Désactive les liens qui entourent un élément éditable
		$(".editable").closest("a").on("click", function(event) { event.preventDefault(); });

		//@todo supp ? car finalement on ajoute le <p> au focus et keyup
		// Ajoute un paragraphe si champs vide au lancement de l'edition
		/*$(".editable").each(function() {
			if($(this).html().length == 0) init_paragraph(this);
		});*/
		
		// Action sur les zone éditable
		$(".editable").on({
			"focus.editable": function() // On positionne la toolbox
			{
				//if(dev) console.log("focus");

				memo_focus = this;// Pour memo le focus en cours

				// Ajoute un <p> si vide
				init_paragraph(this);
			},
			"blur.editable": function() // On clique hors du champ éditable
			{
				//if(dev) console.log("blur");

				if($("#txt-tool:not(:hover)").val()=="") {
					$("#txt-tool").hide();// ferme la toolbox
					$window.off(".scroll-toolbox");// Désactive le scroll de la toolbox
				}

				// Supprime là class qui indique dans quel paragraphe on édite
				$(memo_node).closest("p").removeClass("focus");

				//clean_editable(this);// Nétoie le champ

				// Supprime les br inutiles : nobr
				if(dev) console.log("clean nobr");
				$(".nobr", this).remove();

				// Si juste un paragraphe vide on le supp
				if($("p", this).length == 1 && $("p", this).html() == '') {
					if(dev) console.log("p remove");
					$("p", this).remove();
				}
			},
			"dragstart.editable": function() // Pour éviter les interférences avec les drag&drop d'image dans les champs images
			{
				//if(dev) console.log("dragstart");

				$("body").off(".editable-media");// Désactive les events image
				$("#img-tool").remove();// Supprime la barre d'outil image
			},
			"dragend.editable": function() // drop dragend
			{
				//if(dev) console.log("dragend");

				// Active les events block image
				editable_media_event();
				body_editable_media_event();

				memo_img = null;
				img_leave();// Raz Propriétés image
			},			
			"keyup.editable": function(event)// Mémorise la position du curseur
			{
				//if(dev) console.log("keyup");
				
				// Ajoute un <p> si vide
				init_paragraph(this);

				//@todo remplacer par la fonction selection();
				memo_selection = window.getSelection();				
				if(memo_selection.anchorNode) {
					memo_range = memo_selection.getRangeAt(0);
					memo_node = selected_element(memo_range);//memo_selection.anchorNode.parentElement memo_range.commonAncestorContainer.parentNode
				}

				if(!$(this).hasClass("view-source"))
				{			
					// Mets en évidence le paragraphe éditer
					$("p", this).removeClass("focus");
					$(memo_node).closest("p").addClass("focus");


					// Les éléments dans le node focus
					var node = $(memo_node)[0].childNodes;

					// Si le node contien que un <br>+nobr on le supp
					if(node[0].nodeName == "BR" && node[1])
						if(node[1].nodeName == "BR" && node[1].className == "nobr")
						{
							if(dev) console.log("remove <br>");
							$(node[0]).remove();					
						}


					// Enter
					if(event.keyCode == 13)
					{
						clean_return(".highlight", $(memo_node));// Permet de sortir des highlight
						clean_return(".grid", $(memo_node));// Permet de sortir des grid

						// Si pas shift+enter (saut de ligne simple => <br>)
						if(!event.shiftKey) 
						{
							clean_blockquote($(memo_node));// Permet de sortir des blockquote

							clean_figcaption($(memo_node));// Permet de sortir des figcaption


							// Clean les BR de fin de paragraphe quand on rentre dedans
							if($(memo_node)[0].nodeName == "P" && node[node.length-1].nodeName == "BR") {
								if(dev) console.log("nobr");
								$(node[node.length-1]).addClass("nobr");							
							}


							// Clean les multiples <br> à la fin du précédent <p>
							if($(memo_node).prev("p")[0] != undefined) 
							{
								var node = $(memo_node).prev("p")[0].childNodes;
								if(node && node[node.length-1].nodeName == "BR")
								{
									if(dev) console.log("prev nobr");

									// Si reste un <br> on le met en class=nobr
									if(node[node.length-1].nodeName == "BR")
										$(node[node.length-1]).addClass("nobr");

									if(node[node.length-2].nodeName == "BR")
									{
										if(dev) console.log("prev remove <br>");

										// Avant dernier <br>
										$(node[node.length-2]).remove();

										// Si avant avant dernier <br>
										if(node[node.length-2].nodeName == "BR")
											$(node[node.length-2]).remove();
									}
								}
							}
						}
					}
											
					// Supprime les <br> en fin de <p>		
					//clean_last_br($(memo_node));

					// Constrole les saut de ligne vide // enter = 13 | up = 38 | down : 40
					// || event.keyCode == 40
					//if(event.keyCode == 13) clean_br($(memo_node).prev());
					//if(event.keyCode == 38) clean_br($(memo_node).next());

					// Replace les <div> par des <p>
					if($(memo_node).prop("tagName") == "DIV") 
						clean_div($(memo_node));						
				}
			},
			"click.editable": function(event)// Désactive les ouvertures de liens sous ie
			{
				//if(dev) console.log("click");

				event.preventDefault();
			},
			"mouseup.editable": function(event)// Si on click dans un contenu éditable
			{		
				//if(dev) console.log("mouseup");

				$("#txt-tool .option").hide();// Cache le menu d'option		

				// @todo voir pour get selection + init toolbox quand on termine la selection hors du champs editable
				selection();// Récupère la sélection

				// Si la toolbox est autorisé
				if(!$(this).hasClass("notoolbox"))
				{
					adminbar_height = $("#admin-bar").outerHeight();
					//this_offset_top = $(memo_focus).offset().top;
					//this_left = $(this).offset().left;

					toolbox_height = $("#txt-tool").outerHeight();

					// Si déjà un contenu
					if(memo_range && memo_range.getClientRects()[0] != undefined)
						this_top = memo_range.getClientRects()[0].top + $window.scrollTop();// position du caractère + scroll
					else
						this_top = $(memo_focus).offset().top;// position de la div/tag

					this_top_scroll = this_top - toolbox_height - 12;

					this_left = (memo_range && memo_range.getClientRects()[0] != undefined ? memo_range.getClientRects()[0].left : $(this).offset().left) - 12;

					// Si on est en mode view source on colore le bt view-source
					if($(memo_focus).hasClass("view-source"))
						$("#view-source").addClass("checked");
					else
						$("#view-source").removeClass("checked");

					// Affichage de la boîte à outils texte
					if($("#txt-tool").css("display") == "none")// Si pas visible // init			
					$("#txt-tool")
						.show(function(){
							// Positionnement en fonction de la hauteur de la toolbox une fois visible
							toolbox_height = $("#txt-tool").outerHeight();
							this_top_scroll = this_top - toolbox_height - 12;

							toolbox_position(this_top_scroll, this_left);
						});	

					// Positionnement de la toolbox si déjà affiché et gestion du Scroll si on descend
					$window.on("scroll click.scroll-toolbox", function(event) {
						// Si (Hauteur du scroll + hauteur de la bar d'admin en haut + hauteur de la toolbox + pico) > au top de la box editable = on fixe la position de la toolbox en dessou de la barre admin
						if(($window.scrollTop() + toolbox_height + 12) > this_top_scroll) 
							toolbox_position(adminbar_height, this_left, "fixed");
						else 
						{
							toolbox_position(this_top_scroll, this_left, "absolute");

							// On attend que l'animation soit fini pour voir si on redéplace la toolbox
							setTimeout(function() {
								new_toolbox_height = $("#txt-tool").outerHeight();

								if(toolbox_height != new_toolbox_height){// Nouvelle taille de la toolbox ?
									toolbox_height = new_toolbox_height;
									this_top_scroll = this_top - toolbox_height - 12;
									toolbox_position(this_top_scroll, this_left, "absolute");
								}
							}, 200);	
						}
					});
				}


				// Si on est sur un h2/3 on check l'outil dans la toolbox
				if($(memo_node).closest("h2").length) $("#txt-tool #h2").addClass("checked");
				else $("#txt-tool #h2").removeClass("checked");
					
				if($(memo_node).closest("h3").length) $("#txt-tool #h3").addClass("checked");
				else $("#txt-tool #h3").removeClass("checked");

				if($(memo_node).closest("h4").length) $("#txt-tool #h4").addClass("checked");
				else $("#txt-tool #h4").removeClass("checked");

				if($(memo_node).closest("h5").length) $("#txt-tool #h5").addClass("checked");
				else $("#txt-tool #h5").removeClass("checked");

				if($(memo_node).closest("h6").length) $("#txt-tool #h6").addClass("checked");
				else $("#txt-tool #h6").removeClass("checked");

				if($(memo_node).closest("p").length) $("#txt-tool #p").addClass("checked");
				else $("#txt-tool #p").removeClass("checked");

				if($(memo_node).closest("blockquote").length) $("#txt-tool #blockquote").addClass("checked");
				else $("#txt-tool #blockquote").removeClass("checked");

				if($(memo_node).closest("q").length) $("#txt-tool #q").addClass("checked");
				else $("#txt-tool #q").removeClass("checked");

				//if($(memo_node).closest("p, div").hasClass("highlight")) $("#txt-tool #tool-highlight").addClass("checked");
				if($(memo_node).closest(".highlight").length) $("#txt-tool #tool-highlight").addClass("checked");
				else $("#txt-tool #tool-highlight").removeClass("checked");
				
				if(typeof toolbox_color != 'undefined')
				if($(memo_node).is("em[class^=color-]")) {
					// De-selectionne les couleurs
					$('.toolbox #color-option button[class^=color-]').removeClass('checked');

					// Ouvre le choix des couleur
					$('#txt-tool #color-option').show();

					// Check la couleur en cours
					$("#txt-tool #color-option ."+$(memo_node).attr("class").match(/color-[\w-]+/)).addClass("checked");
				}


				if($(memo_node).closest("li").length) $("#txt-tool #insertUnorderedList").addClass("checked");
				else $("#txt-tool #insertUnorderedList").removeClass("checked");


				// Mets en évidence le paragraphe éditer
				$("p", this).removeClass("focus");
				$(memo_node).closest("p").addClass("focus");
					

				// Désélectionne les alignements
				$("[class*='fa-align']").parent().removeClass("checked");
				
				var align = null;

				// On cherche le type d'alignement si on est dans un bloc aligné avec les style // Ancienne méthode d'alignement 22-04-2022
				if($(memo_node).closest("div [style*='text-align']")[0]) 
					var align = $(memo_node).closest("div [style*='text-align']").css("text-align");
				
				// On cherche le type d'alignement si on est dans un bloc aligné avec align= // Ancienne méthode d'alignement 22-04-2022
				if($(memo_node).closest("div [align]")[0]) 
					var align = $(memo_node).closest("div [align]").attr("align");

				// On cherche les aligements par class
				if($(memo_node).closest("p, div, h2, h3, h4").hasClass("tl")) var align = "tl";
				if($(memo_node).closest("p, div, h2, h3, h4").hasClass("tc")) var align = "tc";
				if($(memo_node).closest("p, div, h2, h3, h4").hasClass("tr")) var align = "tr";

				switch (align) {
					case 'left': align = 'tl'; break;
					case 'center': align = 'tc'; break;
					case 'right': align = 'tr'; break;
				}

				// On check le bon alignement
				if(align) $("#tool-"+align).addClass("checked");


				// Si on sélectionne un contenu
				//if(memo_selection.toString().length > 0)
				{
					// Si on est sur une ancre on ouvre le menu ancre en mode modif
					if($(memo_node).closest("a[name]").length) anchor_option();
					// Si on est sur un lien on ouvre le menu lien en mode modif
					else if($(memo_node).closest("a").length) link_option();

					// Si on est sur un texte dans une langue spécifique on ouvre le menu langue en mode modif
					if($(memo_node).closest("span[lang]").length) lang_option();
				}
				//else memo_selection = memo_range = memo_node = null;// RAZ Sélection		
			}
			
		});
	}

	// Exécute l'event sur les champs éditables
	editable_event();


	// Fonction qui supprime les contenus HTML indésirables sauf ceux autorisés
	function strip_tags(input, allowed) {
		if (input == undefined) return "";

		// Tags en miniature
	    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

	    // Garde uniquement les tags autorisés
	    return input.replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, function ($0, $1) {
	    	return allowed.indexOf("<" + $1.toLowerCase() + ">") > -1 ? $0 : "";
	    });
	}

	// Supprime la mise en forme des contenus copier/coller [contenteditable]
	$(".editable").on("paste", function(event) {
		event.preventDefault();

		// Mode de prélèvement et d'injection
		if($(this).hasClass("view-source")) var getData = "text/plain", insertMode = "insertText";
		else var getData = "text/html", insertMode = "insertHTML";

		// Récupère les contenus du presse-papier // text/html text/plain
		var paste = 
			(event.originalEvent || event).clipboardData.getData(getData) ||
			(event.originalEvent || event).clipboardData.getData("text/plain") ||
			prompt(__("Paste something..."));

		// Supprimes les commentaires HTML
		//paste = paste.replace(/<!--[\s\S]*?-->/gi, "");
		// + Supprime aussi les supportLists de word (Correction de Dominique)
		paste = paste.replace(/<![\s\S]*?>/gi, "");

		// Si PAS en mode visionnage du code source 
		if(!$(this).hasClass("view-source")) 
		{
			// Clean les retours à la ligne |\r
			if(/content="LibreOffice/i.test(paste))		
				paste = paste.replace(/\n/gi, " ")
			else
				paste = paste.replace(/\n/gi, "")

			// Html dans le paste
			if(/<[a-z][\s\S]*>/i.test(paste))
				paste = paste
					.replace(/<p[^>]*><br><\/p><\/div>/gi, "\n")// <br> dans des </p></div>
					.replace(/<p[^>]*><span><\/span><br><\/p>/gi, "\n")// <br> dans des <p> => \n

					//.replace(/<p[^>]*>/gi, "")// Supp les <p> 
					//.replace(/<\/p>/gi, "\n")// Ajoute un saut à la place des <p>

					.replace(/<br>|<\/div>/gi, "\n")// Normalise les objets qui font des retours à la ligne

					// Ancienne méthode qui ne garde pas les name
					//.replace(/<([a-z][a-z0-9]*)(?:[^>]*(\s(src|href)=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/gi, '<$1$2>')// Supprime les formatages dans des balises

					.replace(/<img(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?>/gi, '<img$1>')// Supprime les formatages des img

					.replace(/<a(?:[^>]*(\sname=['\"][^'\"]*['\"]))?(?:[^>]*(\shref=['\"][^'\"]*['\"]))+(?:[^>]*(\sname=['\"][^'\"]*['\"]))?[^>]*?>/gi, '<a$1$2$3>')// Supprime les formatages et garde les href et name

					//.replace(/\<head[^>]*\>([^]*)\<\/head\>/g, '')// Supprime l'entête
					.replace(/\<style[^>]*\>([^]*)\<\/style\>/g, '')// Supprime les styles
					.replace(/\<script[^>]*\>([^]*)\<\/script\>/g, '')// Supprime le js

			// Transforme les retours à la ligne en <br>
			paste = paste.replace(/\n/gi, "<br>");

			// Clean les tags en gardant certain élément de mise en page //@todo voir le cas des <p></p>
			paste = strip_tags(paste, "<p></p><a></a><b><b/><i></i><h1></h1><h2></h2><h3></h3><h4></h4><ul></ul><li></li><br>");
		}

		// Insertion dans le contenu insertHTML insertText
		exec_tool(insertMode, paste);

		// Double switch pour formater en mode source
		if($(this).hasClass("view-source")) {
			view_source(memo_focus);// Mode normal
			view_source(memo_focus);// Mode source formaté
		}
	});


	// Action sur les input de lien si keyup Enter
	$("#txt-tool .option #link, #txt-tool .option #link-text").on("keyup change", function(event)
	{
		// Si lien déjà existant
		if(!$("#txt-tool #link-option button i").is(":visible"))//.hasClass("fa-plus")
		{
			// Change l'intitulé du lien
			if($(memo_node)[0].nodeName == 'SPAN')
				$(memo_node).closest('span').text($("#txt-tool .option #link-text").val());// Si c'est un SPAN
			else
				$(memo_node).closest('a').text($("#txt-tool .option #link-text").val());// Si c'est un A

			// Change le lien
			$(memo_node).closest("a").attr("href", $('#txt-tool .option #link').val());

			// Valide et ferme la box de lien
			if(event.keyCode == 13) $("#txt-tool #link-option").hide("slide", 300);// Cache le menu d'option avec animation
			else			
			tosave();
		}
	});



	/************** FORCE LES IMAGES À LA PLACE DES VIDÉOS YOUTUBE **************/
	$.each($(".video .player-youtube"), function() 
	{
		// Ancienne version non accessible 22/04/2022
		var lazy = ($(this).closest(".editable").hasClass("lazy")?' loading="lazy"':'');

		//$(this).replaceWith('<img src="'+ $(this).data('preview') +'" alt="'+($(this).attr('alt')?$(this).attr('alt'):'')+'" width="'+$(this).attr('width')+'" height="'+$(this).attr('height')+'"'+lazy+'>');

		//$(this).replaceWith('<input type="image" src="'+ $(this).data('preview') +'" title="'+__("Show video")+'" alt="'+$(this).attr('title')+'">');

		$(this).replaceWith('<button title="'+__("Show video")+'"><img src="'+ $(this).data('preview') +'" alt="'+$(this).attr('title')+'" width="320" height="180"'+lazy+'></button>');

		$(".video.play").removeClass("play");
	});



	/************** FORCE LA DÉSACTIVATION LE LAZYLOADING SUR LES IMAGES **************/
	$.each($("img[loading='lazy']"), function() 
	{
		// Assigne les images en attente au src et supprime le lazyloading
		$(this).attr("src", $(this).data("src")).removeAttr("data-src loading");
	});



	/************** IMAGES DANS LES BLOCS TEXTE **************/
	memo_img = null;

	// @todo: voir si on ne peux pas le déplacer sur le blur des event .editable
	$("body").on("click", function(event) {
		// Si on n'est pas sur une image dans un contenu éditable ou edition du alt on supp la toolbox image
		if(!$(event.target).is('.editable img, #alt')) img_leave();// Raz Propriétés image
		
		// Supprime le layer de redimensionnement d'image
		if($("#resize-tool").html() != undefined && !$(event.target).closest("#resize-tool").is('#resize-tool')) {
			$("#resize-tool").remove();
			$(".dialog-media li.select").removeClass("select");// Deselectionne l'image
		} 
	});

	// Affiche les options de gestion d'alignement sur les images ajouter
	$(".editable").on("click", "img, [type='image']", function(event) {

		event.stopPropagation();

		// Si c'est un input type=image
		var input = false;
		if($(this).attr("type") == "image") input = true;
		else if($(this).parent().prop("tagName") == "BUTTON") input = true;

		// Supprimer le précédent bloc d'outils
		$("#img-tool").remove();

		// Masque la toolbox d'edition des textes
		$("#txt-tool").hide();// ferme la toolbox
		
		// Mémorise l'image sélectionnée
		memo_img = this;		
		
		// On ajoute le resizer jquery 
		//if($.browser.webkit)// Si Chrome // Visiblement Firefox n'a plus d'outil de resize...
		if(!input)
		{
			// Rend l'image resizeable
			$(this).resizable({aspectRatio: true});
			
			// Pour styler le block image sélectionné (Même alignement et display entre le div et l'img)
			$(this).parent(".ui-wrapper").addClass($(this).attr('class'));
			$(this).parent(".ui-wrapper").css('display', $(this).css('display'));
		}
		
		// Boîte à outils image
		option = "<ul id='img-tool' class='toolbox'>";

		if(!input)
		{
			option+= "<li><button onclick=\"img_position('fl')\" class='img-position' id='img-fl'><i class='fa fa-fw fa-align-left'></i></button></li>";
			option+= "<li><button onclick=\"img_position('center')\" class='img-position' id='img-center'><i class='fa fa-fw fa-align-center'></i></button></li>";
			option+= "<li><button onclick=\"img_position('fr')\" class='img-position' id='img-fr'><i class='fa fa-fw fa-align-right'></i></button></li>";

			if(typeof toolbox_figure != 'undefined') option+= "<li><button onclick=\"img_figure()\" id='img-figure'>"+ __("Caption") +"</button></li>";
		}

			option+= "<li class=''><input type='text' id='alt' placeholder=\""+ __("Alt text") +"\" title=\""+ __("Alt text") +"\" class='w150p small'></li>";

		if(!input)
			option+= "<li><button onclick=\"img_remove()\" title=\""+ __("Delete image") +"\"><i class='fa fa-fw fa-trash'></i></button></li>";

		option+= "</ul>";

		$("body").append(option);

		// Récupère le texte du l'alt de l'image sélectionné pour le mettre dans les options d'édition de l'alt
		$("#alt").val($(memo_img).attr('alt'));
		

		// Si l'image est encadrer par une <figure> pour un <figcaption>
		if($(memo_img).closest("figure").length) $("#img-figure").addClass("checked");	
		else $("#img-figure").removeClass("checked");


		// Alignement de l'image
		$("#img-position").removeClass("checked");
		if($(memo_img).hasClass("fl") || $(memo_img).closest("figure").hasClass("fl")) $("#img-fl").addClass("checked");
		if($(memo_img).hasClass("center") || $(memo_img).closest("figure").hasClass("center")) $("#img-center").addClass("checked");
		if($(memo_img).hasClass("fr") || $(memo_img).closest("figure").hasClass("fr")) $("#img-fr").addClass("checked");	

		$("#img-tool")
			.show()
			.offset({
				top: ( $(this).offset().top - $("#img-tool").height() - 8 ),
				left: ( $(this).offset().left )
			});
	});

	// Si on tape au clavier on ajoute le texte alt à l'image
	$("body").on("keyup", "#alt", function(event) {
		$(memo_img).attr('alt', $(this).val());
	});



	/************** IMAGE/FICHIER SEUL **************/

	// Charge les images en lazy load pour qu'elles puissent être sauvegardé
	$("[loading='lazy']").each(function() {
		$(this).attr("src", $(this).data("src"));
	});

	
	// On highlight les zones où l'on peut droper des fichiers
	body_editable_media_event = function() {
		$("body")
			.on({
				// Highlight les zone on hover dragover/dragenter
				"dragover.editable-media": function(event) {
					event.stopPropagation();
					$(".editable").off();// Désactive les events sur les contenu éditables
					$(".editable-media").addClass("drag-zone");
					$(".editable-media img, .editable-media i").addClass("drag-elem");
				},
				// Clean les highlight on out
				"dragleave.editable-media": function(event) {
					event.stopPropagation();
					editable_event();// Active les events sur les contenus éditables
					$(".editable-media").removeClass("drag-zone");
					$(".editable-media img, .editable-media i").removeClass("drag-elem");
				}
			});
	}

	// Exécute l'event sur le body pour les images/fichiers
	body_editable_media_event();



	// Rends éditables les images/fichiers
	editable_media_event = function()
	{
		// Icone d'upload + supp du fichier + alt éditable
		$(".editable-media").append(function()
		{
			// Si pas d'option de suppression et d'alt éditable on l'ajoute
			if(!$(this).hasClass("editable-alt")) 
			{
				var alt = $('img', this).attr("alt");

				print_size = null;
				if($(this).data("width")) print_size = $(this).data("width")+'';
				if($(this).data("width") && $(this).data("height")) print_size+=" x ";
				if($(this).data("height")) print_size+= $(this).data("height")+'';

				return "<input type='text' placeholder=\""+ __("Alt text") +"\" class='editable-alt' id='"+ $(this).attr("id") +"-alt' value=\""+ (alt != undefined ? alt : '') +"\">" +
				"<div class='open-dialog-media' title='"+__("Upload file")+"'><i class='fa fa-upload bigger'></i> "+__("Upload file")+"</div>" + 
				"<div class='clear-file' title=\""+ __("Erase") +"\"><i class='fa fa-trash'></i> "+ __("Erase") +"</div>"
				+ (print_size?"<div class='print-size' title=\""+ __("Image dimension in pixel (width x height)") +"\">"+print_size+"</div>":'');
			}
		});

		$(".editable-media")
			.on({
				// Highlight la zone on hover
				"dragover.editable-media": function(event) {
					event.preventDefault();  
					event.stopPropagation();
					$(this).addClass("drag-over");
					$("img, i", this).addClass("drag-elem");
				},
				// Clean le highlight on out
				"dragleave.editable-media": function(event) {
					event.preventDefault();  
					event.stopPropagation();
					$(this).removeClass("drag-over");
					$("img, i", this).removeClass("drag-elem");
				},
				// On lache un fichier sur la zone
				"drop.editable-media": function(event) {
					event.preventDefault();  
					event.stopPropagation();
					$(this).removeClass("drag-over");
					$("img, i", this).removeClass("drag-elem");

					// Upload du fichier dropé
					if(event.originalEvent.dataTransfer) upload($(this), event.originalEvent.dataTransfer.files[0], $(this).hasClass('crop') ? 'crop':true);
				},
				// Hover zone upload	
				"mouseenter.editable-media": function(event) {
					$(this).addClass("drag-over");
					$("img, i", this).addClass("drag-elem");
					$(".open-dialog-media", this).fadeIn("fast");
					$(".print-size", this).fadeIn("fast");// Affiche la taille de l'image/zone

					// Affichage de l'option pour supprimer le fichier si il y en a un
					if($("img", this).attr("src") || $("video", this).attr("src") || $("a i", this).length || $(".fa-doc", this).length)
						$(".clear-file", this).fadeIn("fast");

					// Affiche le alt éditable pour les images
					if($("img", this).attr("src")) {
						// Positionnement
						/*$('#'+ $(this).attr("id") +'-alt').css({
							"width": $("img", this).width(),
							"left": $("img", this).parent().parent().offset().left
						});*/

						// Affichage
						$('#'+ $(this).attr("id") +'-alt').css('display','block');;
					}
				},
				// Out
				"mouseleave.editable-media": function(event) {
					$(this).removeClass("drag-over");
					$("img, i", this).removeClass("drag-elem");
					$(".open-dialog-media", this).hide();
					$(".clear-file, .print-size", this).hide();

					// Masque le alt éditable
					$('#'+ $(this).attr("id") +'-alt').css('display','none');;
				},
				// Ouverture de la fenêtre des médias
				"click.editable-media": function(event) {

					// Suppression
					if($(event.target).hasClass("clear-file") || $(event.target).hasClass("fa-trash")){
						if($("img", this).attr("src")) $("img", this).attr("src","");// Supp img src
						else if($("video", this).attr("src")) $("video", this).remove();// Supp la vidéo
						else {
							$(".fa-doc", this).remove();// Supp le fichier qui vien d'etre ajouté <i>
							$("a", this).remove();// Supp le fichier déjà présent avec lien <a><i>
						} 

						$(".clear-file", this).hide();

						return false;
					}					
					// Edition des alt
					else if($(event.target).hasClass("editable-alt"))
					{
						return false;
					}
					else
					// Ouverture de la fenêtre de média
					{
						// Masque le hover de l'image/fichier sélectionnée
						$(this).removeClass("drag-over");
						$("img, i", this).removeClass("drag-elem");
						$(".open-dialog-media", this).hide();

						// Ouvre la dialog de media
						media(this, 'isolate');
						return false;
					}
				}
			});
	}

	// Exécute l'event sur les images/fichier
	editable_media_event();



	/************** IMAGES BACKGROUND **************/
	
	// Rends éditables les images en background
	editable_bg_event = function() 
	{
		// Bouton d'édition et de suppression
		$("[data-id][data-bg]").append(function()
		{		
			// Si pas d'options d'édition et suppressions, on les ajoute
			if(!$(this).hasClass("editable-bg")) 
			{
				// Ajoute un fond hachuré au cas ou il n'y ait pas de bg
				$("[data-id][data-bg]").addClass("editable-bg");

				// Bouton pour changer le fond
				$("[data-id][data-bg]").append("<div class='bg-tool'><a href=\"javascript:void(0)\" class='open-dialog-media block'>"+__("Change the background image")+" <i class='fa fa-picture'></i></a></div>");

				// S'il y a une image en fond on ajoute l'option de suppression de l'image de fond
				clearbg_bt = "<a href=\"javascript:void(0)\" class='clear-bg' title=\""+__("Delete image")+"\"><i class='fa fa-trash'></i></a>";
				$("[data-id][data-bg]").each(function() {
					if($(this).data("bg"))
						$(".bg-tool", this).prepend(clearbg_bt);
				});
			}
		});

		// Affichage des boutons d'action au survol
		$("[data-id][data-bg]")
			.on({
				"mouseenter.editable-bg": function(event) {// Hover zone upload		
					$("> .bg-tool", this).fadeIn("fast");
				},
				"mouseleave.editable-bg": function(event) {// Out
					$("> .bg-tool", this).fadeOut("fast");
				}
			});		
	}

	// Exécute l'event sur les images
	editable_bg_event();

	// Ouverture de la fenêtre des médias pour changer le bg
	$("body").on("click", ".editable-bg > .bg-tool .open-dialog-media", function() {
		media($(this).parent().parent()[0], 'bg');
	});

	// Supprime l'image de fond
	$("body").on("click", ".editable-bg > .bg-tool .clear-bg", function() {
		$(this).parent().parent().attr('data-bg','').css("background-image","none");
		$(this).remove();
	});


	/************** MODULE DUPLICABLE **************/
	// Change la clé
	edit_key = function()
	{		
		// Modifie les cles
		$("[class*='editable']", this).each(function() {
			old_key = $(this).attr("id");
			if(old_key == undefined) 
				old_key = $("[id*='" + module + "-']", this).attr("id");

			// Pour les inputs editable
			if($(this).attr("placeholder") != undefined) 
				$("#" + old_key).attr("placeholder", $(this).attr("placeholder").replace("-0", "-"+ new_key));

			// Pour les bg editable
			if($(this).attr("data-id")) {
				old_key = $(this).attr("data-id");
				
				$(this).attr({
					'data-id': old_key.replace("-0", "-"+ new_key)
				});
			}
			
			// Change l'id
			$("#" + old_key).attr({
				id: old_key.replace("-0", "-"+ new_key),
				src: ""
			});
		});

		// Relance les events d'edition
		editable_event();
		editable_media_event();
		editable_href_event();
		editable_bg_event();
	}

	// Ajoute un module
	add_module = function(event)
	{
		module = $(event).parent().prev("ul, ol").attr("id");

		// On regarde qu'elle type d’élément éditable existe pour récupérer l'id le plus grand
		if($("#" + module + " li .editable").length) 
			var elem = $("#" + module + " li .editable");
		else if($("#" + module + " li .editable-media").length) 
			var elem = $("#" + module + " li .editable-media");

		// Crée un id unique (dernier id le plus grand + 1)
		//key = parseInt($("#" + module + " li:first-child .editable").attr("id").split("-").pop()) + 1; Ne tien pas compte de l'ordre des id
		new_key = $.map(elem, function(k) {
			return parseInt(k.id.match(/(\d+)(?!.*\d)/));//Récupère le dernier digit de la chaine
		}).sort(function(a, b) {
			return(b-a); // reverse sort : tri les id pour prendre le dernier (le plus grand)
		})[0] + 1;

		// Unbind les events d'edition
		$(".editable").off();
		$(".editable-media").off(".editable-media");
		$(".editable-href").off(".editable-href");
		$(".editable-bg").off(".editable-bg");

		if($("#"+module).hasClass("end"))
			// Crée un block à la fin
			$("#" + module + " > li:last-child").clone().insertBefore("#" + module + " > li:last-child").show("400", edit_key);
		else
			// Crée un block au début
			$("#" + module + " > li:last-child").clone().prependTo("#" + module).show("400", edit_key);
	}

	// Rends déplaçables les blocs
	move_module = function() {

		// Change le style du bouton et l'action
		$(".module-bt .fa-move").css("transform","scale(.5)");

		// Désactive l'edition
		$(".editable-media").off(".editable-media");
		$(".editable").off();

		// Change l'action sur le lien 'move'
		$(".module-bt [href='javascript:move_module();']").attr("href","javascript:unmove_module();");

		// Les rend déplaçable
		$(".module").sortable();
	}

	// Désactive le déplacement des blocs
	unmove_module = function() {

		// Change le style du bouton et l'action
		$(".module-bt .fa-move").css("transform","scale(1)");

		// Change l'action sur le lien 'move'
		$(".module-bt [href='javascript:unmove_module();']").attr("href","javascript:move_module();");

		// Active l'edition
		editable_event();
		editable_media_event();

		// Désactive le déplacement
		$(".module").sortable("destroy");
	}

	// Désactive le lien sur le bloc
	//$(".module li > a").attr("href", "javascript:void(0)").css("cursor","default");

	// Désactive les bulles d'information
	//$(".module li a").tooltip("disable");

	// Désactive les animations pour rendre plus fluide les déplacements et l'edition
	$(".module .fire").css({
		"opacity": "1",
		"transform": "translate3d(0, 0, 0)"
	});
	$(".module .animation").removeClass("animation fire");

	// Ajoute les boutons de mobule
	$(".module", this).each(function() {
		// Ajoute le BOUTON POUR DUPLIQUER le bloc vide de défaut
		$(this).after("<div class='module-bt'"+($(this).hasClass("end")?" style='top:unset;bottom:0;'":"")+"><a href='javascript:move_module();'><i class='fa fa-fw fa-move'></i><span> "+__("Move")+"</span></a> <a href='javascript:void(0)' onclick='add_module(this)'><i class='fa fa-fw fa-plus'></i><span> "+__("Add a module")+"</span></a></div>");

		// Ajoute une marge pour pas que les boutons d'ajout se superpose avec les blocs
		if($(this).hasClass("end"))		
			$(this).css("padding-bottom","4rem");// Bouton en bas
		else				
			$(this).css("padding-top","5rem");// Bouton en haut
	});

	// Force le parent en relatif pour bien positionner les boutons d'ajout
	$(".module-bt").parent().addClass("relative");

	// Ajout de la SUPPRESSION au survole d'un bloc
	$(".module > li").append("<a href='javascript:void(0)' onclick='remove_module(this)'><i class='fa fa-cancel absolute none red' style='top: -5px; right: -5px; z-index: 12;' title='"+ __("Remove") +"'></i></a>");

	// Affiche les boutons de suppression
	//$(".module li .fa-cancel").fadeIn();

	// Fonction pour supprimer un bloc
	remove_module = function(that) {
		//console.log($(that).closest("li"));
		$(that).closest("li").fadeOut("slow", function() {
			this.remove();
		});
	};


	/************** CHAMPS INPUT **************/
	
	// Ajout un placeholder s'il n'y en a pas
	$(".editable-input").not("[placeholder]").each(function() {
		$(this).attr("placeholder", $(this).attr("id")).attr("title", $(this).attr("id"));
	});

	// Si le contenu d'un input change on doit sauvegarder
	$(".editable-input").on("change", function(event) {
		tosave();
	});

	// Transforme les inputs hidden en texte visible
	$("input[type='hidden'].editable-input").attr("type","text");

	$(".editable-select.none").show();

	$(".editable-tag.none").slideDown();
	$(".editable-media .none").show();
	$(".editable-hidden").slideDown();
	$("label.none").slideDown();



	/************** CHAMPS SELECT **************/
	$(".editable-select").attr("data-option", function(i, data) {
		if(data != undefined)
		{
			// Option sélectionnée
			var selected = $(this).attr("data-selected");

			// Extraction du json
			var json = $.parseJSON(data);		

			// Création des options avec le json
			var html = '';
			$.each(json, function(cle, val){ 				
				html += '<option value="'+ cle +'"'+(cle == selected?" selected":"")+'>'+ val +'</option>';
			});
			
			// Les attribue
			var attr = {};
			$.each(this.attributes, function() { attr[this.name] = this.value; });
			
			// Remplace les select
			$(this).replaceWith($("<select/>", attr).html(html));
		}
	})

	// Change le data-selected dynamiquement
	$(".editable-select").on("change", function(event) {
		$(this).attr("data-selected", $(this).val());
		tosave();
	});



	/************** CHAMPS CHECKBOX **************/
	$(".editable-checkbox, .lucide [for]").not(".lucide #admin-bar [for]").on("click", function(event) {
		if($(this).attr("for")) var id = $(this).attr("for");
		else var id = this.id;

		if($("#"+id).attr('type') != 'radio') 
		{
			if($("#"+id).hasClass("fa-ok")) $("#"+id).removeClass("fa-ok yes").addClass("fa-cancel no");
			else $("#"+id).removeClass("fa-cancel no").addClass("fa-ok yes");
		}
	})



	/************** HREF EDITABLE **************/
	
	// Ajoute un input pour ajouter l'url du href
	$("[data-href]").append(function() {
		return "<input type='text' placeholder='"+ __("Destination URL") +"' class='editable-href' id='"+ $(this).data("href") +"' value='"+ ($(this).attr("href")?$(this).attr("href"):'') +"'>";
	});

	// Rends éditables les liens
	// Note: on utilise animate car l'input est inline-block par défaut avec le fadeIn
	editable_href_event = function(event) {
		$("[data-href]")
			.on({
				"click.editable-href": function(event) {// Supprime l'action de click sur le lien
					//event.stopPropagation();// @todo supp car empèche l'édition des bg
					event.preventDefault();
				},
				"mouseenter.editable-href": function(event) {// Hover zone href		
					$(".editable-href", this).animate({'opacity':'1'}, 'fast');
				},
				"mouseleave.editable-href, focusout.editable-href": function(event) {// Out
					if(autocompleteOpen == false) 
						$(".editable-href", this).animate({'opacity':'0'}, 'fast');
				}
			});		
	}

	// Exécute l'event sur les liens éditables
	editable_href_event();



	/************** AUTOCOMPLETE DE SUGGESTION DES PAGES EXISTANTES POUR L'AJOUT DE LIEN **************/
	$(document).on("keydown.autocomplete", "#txt-tool .option #link, .editable-href", function() 
	{
		$(this).autocomplete({
			minLength: 0,
			source: path + "api/ajax.admin.php?mode=links&nonce="+ $("#nonce").val() +"&dir="+ ($(memo_node).closest(".editable").data("dir") || ""),
			select: function(event, ui) 
			{ 
				// S'il y a déjà un chemin présent ont ajouté à la suite avec juste la dernière partie | Cas tag
				if($(this).val().indexOf("/") !== -1)
				{
					// Ajoute le dernier terme au contenu courant (moins la saisie de recherche)
					$(this).val(function(index, value) {
						return value.substring(0, value.lastIndexOf('/')) +'/'+ ui.item.value.split("/").pop();
					});
				}
				else {
					$(this).val(ui.item.value).change();//.change pour forcer l'action .on("change") sur le input
					autocompleteOpen = false;
				}
	
				return false;// Coupe l'execution automatique d'ajout du terme
			},
			close: function(event, ui) {
				autocompleteOpen = false;
			}
		})
		.focus(function(){
			$(this).data("uiAutocomplete").search($(this).val());// Ouvre les suggestions au focus			
		})
		.autocomplete("instance")._renderItem = function(ul, item) {// Mise en page des résultats
			autocompleteOpen = true;
	      	return $("<li>").append("<div title='"+item.value+"'>"+item.label+" <span class='grey italic'>"+item.type+"</span></div>").appendTo(ul);
	    };
	});

    

	/************** SYSTÈME DE TAG / CATÉGORIE **************/

	// Si champs tag
	if($(".editable-tag").length) 
	{
		// Si la liste de tag est dans un ul on l'aplatie
		$(".editable-tag").each(function()
		{
			if($(this).prop("tagName") == "UL")
			{
				// Séparateur
				separator = $(this).data("separator");// Si on en force un
				if(!separator) separator = ", ";// Sinon celui par défaut

				// Ajoute le séparateur
				$("li:not(:last-child)", this).append(", ");

				// Liste le contenu des li
				var li_list = '';
				$("li", this).each(function() {
					li_list+= $(this).html();
				});

				// Remplace le contenu du ul par une liste textuelle
				$(this).html(li_list);

				// Transforme le ul en tagName plus classique pour éviter les bugs de saisie sur FF
				$(this).replaceWith(function(){
					return this.outerHTML.replace("<ul", "<nav").replace("</ul", "</nav")
				});
			}
		});

		// Transforme le champs tag en editable
		$(".editable-tag").attr("contenteditable", "true");

		// AUTOCOMPLETE
		tag_zone = $(".editable-tag").attr('id');
		autocomplete_keydown = false;
		var samezone = false;

		$(".editable-tag").on("keydown", function(event) {				
			// Ne quitte pas le champ lorsque l'on utilise TAB pour sélectionner un élément
			if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active)
				event.preventDefault();	

			autocomplete_keydown = true;// On a fait une saisie au clavier
			
			tosave();// A sauvegarder si on écrit
		})
		.autocomplete({
			minLength: 0,
			source: function(request, response) {

	            // Si les data on déjà était chargé donc on affiche direct le resultat
	            if(samezone && typeof all_data !== 'undefined') {
	            	response($.ui.autocomplete.filter(all_data, request.term.split(regex).pop()));
	            	return;
	            }

				$.ajax({
					type: "POST",
					dataType: "json",
					url: path+"api/ajax.admin.php?mode=tags",
					data: {
						"zone": tag_zone,
						"nonce": $("#nonce").val()
					},
					success: function(data) {
						
						// hide loading image
						//$('input.suggest-user').removeClass('ui-autocomplete-loading');  
                		//response(data);
	                	//response($.map(data, function(item) { }));

						all_data = data;// Pour la mise en cache de la liste complete

						// Déléguer à la saisie semi-automatique et extrait le dernier terme
	                	response($.ui.autocomplete.filter(data, request.term.split(regex).pop()));
		            }
		        });
			},
			focus: function() {
				//$(this).data("uiAutocomplete").search($(this).val());
				return false;// prevent value inserted on focus
			},
			select: function(event, ui) {

				// Crée un tableau avec les éléments déjà présents dans la liste
				if($(this).text()) var terms = $(this).text().split(regex);
				else var terms = [];

				// Supprimer l'entrée actuelle SI on a fait une recherche
				if(autocomplete_keydown) terms.pop();

				// Ajouter l'élément sélectionné
				terms.push(ui.item.value);

				// Ajoute le placeholder pour avoir la virgule+espace à la fin
				//terms.push("");

				// Ajoute le tag
				$(this).text(terms.join(separator));

				// Pour focus à la fin du champ tags
				range = document.createRange();//Create a range (a range is a like the selection but invisible)
		        range.selectNodeContents(this);//Select the entire contents of the element with the range
		        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
		        memo_selection = window.getSelection();//get the selection object (allows you to change selection)
		        memo_selection.removeAllRanges();//remove any selections already made
		        memo_selection.addRange(range);

		        // A sauvegarder
		        tosave();

				return false;
			}
		})
		.focus(function()// Chargement au focus de la liste des tags dispo & récupération des variables
		{
			// Séparateur
			separator = $(this).data('separator');// Si on en force un
			if(!separator) separator = ', ';// Sinon celui par défaut
			regex = separator.replace(" ", "\\s*");// Replace les espaces par des espaces optionnels
			regex = new RegExp(regex, "g");// Crée une regex avec la string

			// Si on sélectionne la même zone de tag
			if(tag_zone == $(this).attr('id')) {
				samezone = true;
			} else {
				tag_zone = $(this).attr('id');
				samezone = false;
			}

			$(this).autocomplete("search", "");
		});
	}


	/************** LISTE DES CONTENUS **************/

	// Ouverture de la liste des contenus
	$("#list-content i").on("click",
		function(event) {
			$.ajax({
				type: "POST",
		        url: path+"api/ajax.admin.php?mode=list-content",
				data: {"nonce": $("#nonce").val()},
				success: function(html)
				{				
					$("body").append(html);

					$(".dialog-list-content").dialog({
						autoOpen: false,
						//modal: true,
						width: 'auto',
						maxWidth: '50%',
		        		position: { my: "left+10 top", at: "left bottom+10", of: $("#admin-bar") },
						show: function() {$(this).fadeIn(300);},
						close: function() { $(".dialog-list-content").remove(); }
					});

					$(".dialog-list-content").dialog("open");
				}
		    });
		}
	);


	/************** USERS **************/

	// Ouverture de l'admin des users
	$("#user i").on("click mouseenter",	// touchstart
		function(event) {

			event.stopPropagation();
			event.preventDefault();		

			// Pour ne pas ouvrir les meta du title si l'admin user ouverte	
			$("#meta").addClass("nofire");

			if(!$("#user .absolute").length && event.type == "mouseenter")
			{
				$.ajax({
					type: "POST",
					url: path+"api/ajax.php?mode=user",
					data: {"nonce": $("#nonce").val()},
					success: function(html){ 
						$("#user").append(html);						
						
						// Pour fermer l'admin user quand on click en dehors
						close = false;
						$(document).on("click",	
							function(event) {
								if(!$(event.target).parents().is("#user .absolute") && $("#user .absolute").is(":visible") && close == false)//event.type == 'click'
									if($("#user button.to-save").length || $("#user button i.fa-spin").length)// Si fiche pas sauvegardé on shake
										$("#user .absolute > div").effect("highlight");
									else {
										$("#user .absolute").fadeOut("fast", function(){ close = true; });
										$("#meta").removeClass("nofire");// Remets le champ meta en hover disponible
									}
							}
						);
					}
				});
			}
			else if($("#user .absolute").length && !$("#user .absolute").is(":visible") && close == true)// Si on click et que l'ajax a déjà été fait
			{
				close = true;
				$("#user .absolute").fadeIn("fast", function(){ close = false; });
			}
			else if(event.type == 'click' && $("#user .absolute").is(":visible") && close == false )// Si on click sur le bt user de l'admin-bar
			{
				$("#user .absolute").fadeOut("fast", function(){ close = true; });
				$("#meta").removeClass("nofire");// Remets le champ meta en hover disponible
			}
		}
	);


	/************** EXECUTION DES FONCTIONS D'EDITION DES PLUGINS **************/
	$(edit).each(function(key, funct){
		funct();
	});


	/************** ACTION **************/

	// Si on ferme l'admin
	$("#close").on("click", function() {	
		reload();
	});


	// Archive du contenu
	$("#archive").on("click", function() 
	{	
		// Dialog de confirmation d'archive 
		$("body").append("<div class='dialog-archive' title='"+ __("Archive the page") + ' \"' + document.title.replace(/'/g, '&apos;') + "\" ?'><p><i class='fa fa-attention red biggest mrs' aria-hidden='true'></i>"+__("Archive the page")+" : <b>"+document.title+"</b> ?</p></div>");

		// Dialog d'archive
		$(".dialog-archive").dialog({
			modal: true,
			buttons: 
			[{
            	text: __("Cancel"),
           		click: function() { $(".dialog-archive").remove(); }
			},{
				text: "Ok",
				click: function() 
				{
					// Fonction à exécuter avant l'archivage de la page
					$(before_del).each(function(key, funct){ funct(); });

					// Requete de suppression
					$.ajax({
						type: "POST",
						url: path + "api/ajax.admin.php?mode=archive",
						data: {
							"url": clean_url(),
							"type": type,
							"id": id,
							"nonce": $("#nonce").val()// Pour la signature du formulaire
						}
					})
					.done(function(html) {		
						// Fonction à exécuter après l'archivage de la page
						$(after_del).each(function(key, funct){ funct(); });

						$(".dialog-archive").dialog("close");
						$("body").append(html);
					});					
				}
			}],
			close: function() {
				$(".dialog-archive").remove();					
			}
		});
	});


	// Suppression du contenu
	$("#del").on("click", function() 
	{	
		medias = {};

		// Contenu des images éditables, bg et dans les contenus textuels
		$(document).find(".content .editable-media img, .content .editable img, .content [data-id][data-bg]").each(function() {
			if($(this).hasClass("editable-bg")) var media = $(this).attr("data-bg");
			else var media = $(this).attr("src");

			// et si le média est bien sur le site/dossier en cours
			if(media && media.indexOf(media_dir) !== -1) medias[media] = "img";
		});

		// Contenu des fichiers éditables et dans les contenus textuels
		$(document).find(".content .editable-media .fa, .content .editable a[href^='"+media_dir+"/']").each(function() {
			if($(this).closest("span").hasClass("editable-media")) var media = $(this).attr("title");
			else var media = $(this).attr("href");

			// et si le média est bien sur le site/dossier en cours
			if(media && media.indexOf(media_dir) !== -1) medias[media] = "fichier";
		});


		// Clean les url pour supprimer l'host et les données après le "?"
		medias_clean = {};
		host = location.protocol +'//'+ location.host + path;
		$.each(medias, function(media, type) {
			media = path + media.replace(host, "").split("?")[0];
			medias_clean[media] = type;			
		});


		// Dialog de confirmation de suppression 
		$("body").append("<div class='dialog-del' title='"+ __("Delete the page") + ' \"' + document.title.replace(/'/g, '&apos;') + "\" ?'><p><i class='fa fa-attention red biggest mrs' aria-hidden='true'></i>"+__("Warning, this will permanently delete the page")+" : <b>"+document.title+"</b></p></div>");

		// S'il y a des médias à supprimer
		if(Object.keys(medias_clean).length > 0)
		{
			// Option de suppression des média liée au contenu			
			$(".dialog-del").append("<hr><input type='checkbox' id='del-medias' class='inline'> <label for='del-medias' class='inline'>"+ __("Also remove media from content") +"</label><ul class='unstyled man'></ul>");

			// Affiche la liste des medias
			$.each(medias_clean, function(media, type) {
				if(type == "img") $(".dialog-del ul").append('<li><label for="'+ media +'"><img src="'+ media +'" title="'+ media +'"></label> <input type="checkbox" class="del-media" id="'+ media +'"></li>');
				else $(".dialog-del ul").append('<li><label for="'+ media +'"><i class="fa fa-fw fa-doc biggest" title="'+ media +'"></i></label> <input type="checkbox" class="del-media" id="'+ media +'"></li>');
			});

			// Au click sur la checkbox générale on coche tous les médias ont supprimé
			$("#del-medias").on("click", function() {	
				if($(this).prop("checked") == true) $(".del-media").prop("checked", true);
				else $(".del-media").prop("checked", false);
			});

			// Au click sur une checkbox de média on vérifie si tous est coché et on coche la checkbox générale
			$(".del-media").on("click", function() {	
				if($(".del-media:checked").length == $(".del-media").length) $("#del-medias").prop("checked", true);
				else $("#del-medias").prop("checked", false);
			});
		}


		// Dialog de suppression
		$(".dialog-del").dialog({
			modal: true,
			buttons: 
			[{
            	text: __("Cancel"),
           		click: function() { $(".dialog-del").remove(); }
			},{
				text: "Ok",
				click: function() 
				{
					// Fonction à exécuter avant la suppression de la page
					$(before_del).each(function(key, funct){ funct(); });

					// Récupère tous les médias sélectionnés
					medias_post = [];
					$(".dialog-del ul input:checked").each(function() {
					    medias_post.push($(this).attr("id"));
					});

					// Requete de suppression
					$.ajax({
						type: "POST",
						url: path + "api/ajax.admin.php?mode=delete",
						data: {
							"url": clean_url(),
							"type": type,
							"id": id,
							"medias": medias_post,
							"nonce": $("#nonce").val()// Pour la signature du formulaire
						}
					})
					.done(function(html) {		
						// Fonction à exécuter après la suppression de la page
						$(after_del).each(function(key, funct){ funct(); });

						$(".dialog-del").dialog("close");
						$("body").append(html);
					});					
				}
			}],
			close: function() {
				$(".dialog-del").remove();					
			}
		});
	});


	// On change une info dans un menu select
	$("#admin-bar select").change(function() {
		tosave();// A sauvegarder
	});


	// Si on change le statut d'activation
	$("#state-content").on("click", function() {

		// Masque la bulle admin qui indique si la page est désactivée
		if($("#admin-bar #state-content").prop("checked") == true)
			$(".bt.fixed.construction").fadeOut();
		else 
			$(".bt.fixed.construction").fadeIn();

		tosave();
	});


	// Si on sauvegarde
	$("#save").on("click", function() {	
		save();
	});


	// Capture des actions au clavier // keydown keypress
	$(document).on("keydown", function(event) 
	{
		// Si on appuie sur ctrl + s = sauvegarde
		if(typeof shortcut !== 'undefined' && (event.ctrlKey || event.metaKey)) 
		{
			// Sauvegarde
			if(String.fromCharCode(event.which).toLowerCase() == 's') {
				event.preventDefault();
				save();		
			}
		
			// Active la page
			if(String.fromCharCode(event.which).toLowerCase() == 'q') {
				event.preventDefault();
				if($("#admin-bar #state-content").prop("checked") == false) 
				{
					$("#admin-bar #state-content").prop("checked", true);

					$(".bt.fixed.construction").fadeOut();// Masque la bulle info activation
				}
				else {
					$("#admin-bar #state-content").prop("checked", false);

					$(".bt.fixed.construction").fadeIn();// Affiche la bulle info activation
				}

				tosave();
			}
		}
		// Si on tape du texte dans un contenu éditable on change le statut du bouton sauvegardé
		else if(event.target.className.match(/(editable)/) || event.target.id.match(/^(title|description|permalink)$/)) 
		{	
			// Caractères texte ou 0/96 ou entrée
			if(String.fromCharCode(event.which).match(/\w/) || event.keyCode == 96 || event.keyCode == 13)
			{
				tosave();// A sauvegarder
			}
			else if(event.keyCode == 46 || event.keyCode == 8)// Suppr ou Backspace
			{
				tosave();// A sauvegarder
				
				// Si on est sur une image est que l'on clique sur Supp on supprime l'image du contenu
				if(memo_img) {
					event.preventDefault();
					img_remove();
				}
			}			
		}
	});


	// Si Chrome on supprime les span qui s'ajoutent lors des suppressions de retour à la ligne (ajoute une font-size)
	if($.browser.webkit) {
		$("[contenteditable=true]").on("DOMNodeInserted", function(event) {
			// Si c'est un span, sans langue, sans editable
			if(
				event.target.tagName == "SPAN"
				&& event.target.lang == ""
				&& !$(event.target).hasClass("editable")
				&& !$(event.target).hasClass("editable-tag")
			) {
				event.target.outerHTML = event.target.innerHTML;			
			}
		});
	}


	// Désactive le click pour ne pas relancer l'admin
	$(".bt.edit").off("click");

});	
