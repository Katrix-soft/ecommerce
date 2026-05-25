import os
import json
import argparse
from typing import List
from pydantic import BaseModel, Field
from markitdown import MarkItDown
import google.generativeai as genai

class ProductoSchema(BaseModel):
    id_interno: int = Field(description="ID autogenerado si existe, o dejar en 0")
    sku: str = Field(description="Código SKU del producto único. Limpiar espacios.")
    nombre: str = Field(description="Nombre completo del producto, incluyendo especificaciones o variantes.")
    precio: float = Field(description="Precio numérico final. Quitar símbolos de moneda ($) y comas de miles.")

class ListaProductos(BaseModel):
    productos: List[ProductoSchema]

def parsear_documento_a_productos(file_path: str) -> str:
    md = MarkItDown()
    return md.convert(file_path).text_content

def estructurar_con_llm(texto_markdown: str) -> dict:
    genai.configure(api_key=os.environ.get("GEMINI_API_KEY", ""))
    
    prompt = f"""
    Analiza el siguiente texto extraído de un documento de proveedor y estructura la información 
    en una lista de productos válida según el esquema JSON solicitado.
    
    Asegúrate de limpiar los precios (convertir a float puro) y extraer correctamente los SKUs.
    
    Documento extraído:
    ---
    {texto_markdown}
    ---
    """
    
    model = genai.GenerativeModel("gemini-1.5-flash")
    
    response = model.generate_content(
        prompt,
        generation_config=genai.types.GenerationConfig(
            response_mime_type="application/json",
            response_schema=ListaProductos,
            temperature=0.0
        ),
    )
    
    return json.loads(response.text)

def process_file(file_path: str) -> dict:
    markdown_text = parsear_documento_a_productos(file_path)
    return estructurar_con_llm(markdown_text)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Procesa documentos de proveedores a JSON usando IA.")
    parser.add_argument("file", help="Ruta al archivo del proveedor (ej. .xlsx, .pdf, .docx)")
    args = parser.parse_args()
    
    try:
        resultado = process_file(args.file)
        print(json.dumps(resultado, indent=2, ensure_ascii=False))
    except Exception as e:
        print(json.dumps({"error": str(e)}, indent=2, ensure_ascii=False))
