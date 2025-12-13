import {HoverOnInformationAgent, HoverOnInformationContact} from "./info-agent-or-contact-modal.js";


new HoverOnInformationAgent({
	containerSelector:'#example',
	hoverAttribute:'data-hover-agent',
	modalClass:'info-agent-modal',
}); // Для агентів
new HoverOnInformationContact({
	containerSelector:'#example',
	hoverAttribute:'data-hover-contact',
	modalClass:'info-contact-modal',
}); // Для контактів
