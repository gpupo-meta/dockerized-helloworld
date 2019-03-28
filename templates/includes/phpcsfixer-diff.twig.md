
```diff
<?php

+declare(strict_types=1);
+
+/*
+ * This file is part of gpupo/dockerized-helloworld
+ * Created by Gilmar Pupo <contact@gpupo.com>
+ * For the information of copyright and license....
+ *
+ */
+
 namespace Gpupo\DockerizedHelloworld\Traits;

-use JMS\Serializer\Annotation as JMS,
-    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
-    PDO;
+use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
+use JMS\Serializer\Annotation as JMS;

 /**
- * Very wrong code style
- *
- *
- *
+ * Very wrong code style.
  */
-trait VeryWrongCodeStyleTrait {
-
+trait VeryWrongCodeStyleTrait
+{
     /**
      * @var string
      * @ODM\Field(type="string")
     private $name;

     /**
-     * Set name
+     * Set name.
+     *
+     * @param string $name
      *
-     * @param  string $name
      * @return mixed
      */

```
