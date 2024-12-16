# Technical_test_BJunior
Prueba técnica en PHP/Laravel para evaluar conocimientos en los requisitos requeridos
# Desarrollo de la prueba técnica
## 1. Almacenamiento de Información Adicional en Comprobantes
- La API debe permitir guardar datos adicionales al registrar comprobantes, incluyendo serie, número, tipo de comprobante y moneda. Además, se requiere regularizar los comprobantes existentes, extrayendo esta información desde el campo xml_content de la tabla vouchers para integrarla en el sistema.

### Paso 1: Crear una migración para agregar los campos necesarios a la tabla vouchers
```php
php artisan make:migration add_fields_to_vouchers_table
```

> [!NOTE]  
> En el archivo de migración agregamos los campos **serie**, **numero**, **tipo** y **moneda** a la tabla **vouchers**.



## 2. Procesamiento Asíncrono de Comprobantes
- Actualmente, el registro de comprobantes se ejecuta en primer plano. Este proceso debe modificarse para realizarse en segundo plano mediante procesamiento asíncrono, mejorando así la eficiencia y respuesta de la API
- También es necesario actualizar el resumen enviado por correo al finalizar el procesamiento, de manera que incluya:
    - Un listado de comprobantes registrados exitosamente.
    - Un listado de comprobantes que no se pudieron registrar, indicando la razón de cada fallo.
## 3. Consulta de Montos Totales Acumulados por Moneda
- Implementar un endpoint que permita consultar los montos totales acumulados de los comprobantes registrados por el usuario autenticado, desglosados en soles y dólares. No se requiere realizar una conversión entre las divisas.
## 4. Eliminación de Comprobantes por Identificador
- Incorporar una funcionalidad que permita al usuario autenticado eliminar comprobantes específicos utilizando su identificador (ID). Esto deberá aplicarse solo a los comprobantes registrados por el mismo usuario, respetando las reglas de autorización.
## 5. Filtros Avanzados en la Consulta de Comprobantes
- Modificar el endpoint de listado de comprobantes para que soporte los siguientes filtros opcionales: serie, número, tipo de comprobante, moneda y rango de fechas. El rango de fechas es el único filtro obligatorio y debe aplicarse sobre la fecha de registro de los comprobantes. Además, la consulta debe limitarse para que el usuario autenticado solo pueda acceder a los comprobantes que él mismo haya registrado.