
	make relk@up

Logstash config: :whale:

```bash
curl -XPOST -D- 'http://kibana:5601/api/saved_objects/index-pattern' \
	-H 'Content-Type: application/json' \
	-H 'kbn-version: 6.2.4' \
	-d '{"attributes":{"title":"logstash-*","timeFieldName":"@timestamp"}}'
```
