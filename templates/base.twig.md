{% macro add_img(filename, legend = '') %}
![{{legend}}](https://meta.gpupo.com/dockerized-helloworld/img/{{filename}})
{% endmacro %}

{% import _self as base %}

# Main Quest
{% block main_quest %}
{% endblock %}

{{ base.add_img('pizzatime.jpg', 'Cya image') }}
