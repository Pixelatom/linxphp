Hooks are basically files included at the start of the framework. These files are used to specify hook that will be executed when some event occurs

Predefined Events

system.ready
Apenas se termina de cargar el framework
system.routing
Ejecutado cuando se llama a 'delegate'
system.execute
Cuando el router encontro que tiene que ejecutar y esta a punto de crear la clase y ejecutar su metodo
system.post_routing
Al finalizar un ruteo
system.404
Cuando el router no encontró un controller para ejecutar
template.show_call
Cuando se llama a la funcion show de un template, parametro $name, es el parametro que se le pasa a la funcion show
template.show
Ya se proceso el template y se esta a punto de mostrar el resultado, parametros $output (por referencia) y $name