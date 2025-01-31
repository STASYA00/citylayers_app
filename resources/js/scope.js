class Scope extends CElement{
    constructor(parent){
        super(parent);
        this.name = "scope";
        this.content = "images/scope.svg"; // U+02715
    }

    getParent(){
        let elements = document.getElementsByClassName(this.parent);
        if (elements.length>0){
            return elements[0];
        }
    }

    initiate() {
        var element = document.createElement("img");
        element.src = this.content;
        element.setAttribute('class', this.name);
        element.setAttribute("id", this.make_id());
        this.getParent().appendChild(element);
    }
}