# TranslateAPI Java SDK

Java client for TranslateAPI translation service.

## Installation

Add to your `pom.xml`:

```xml
<dependency>
    <groupId>com.forcekeys</groupId>
    <artifactId>translate-api</artifactId>
    <version>1.0.0</version>
</dependency>
```

Or clone from GitHub and build:

```bash
git clone https://github.com/forcekeys/translate-api-java.git
cd translate-api-java
mvn install
```

## Usage

```java
import com.forcekeys.translateapi.TranslateAPI;
import com.forcekeys.translateapi.TranslationResult;

public class Example {
    public static void main(String[] args) {
        TranslateAPI api = new TranslateAPI("your_api_key_here");
        
        // Translate text
        TranslationResult result = api.translate("Hello world", "en", "fr");
        System.out.println(result.getTranslatedText());
        
        // Detect language
        String detected = api.detect("Bonjour");
        System.out.println(detected);
        
        // Get languages
        List<Language> languages = api.languages();
        for (Language lang : languages) {
            System.out.println(lang.getCode() + ": " + lang.getName());
        }
        
        // Get account info
        AccountInfo account = api.account();
        System.out.println("Credits: " + account.getCredits());
    }
}
```

## API Reference

### TranslateAPI

```java
TranslateAPI api = new TranslateAPI(apiKey, baseUrl);
```

#### Methods

- `translate(text, source, target)` - Translate text
- `translateFile(filename, source, target, output)` - Translate file
- `detect(text)` - Detect language
- `languages()` - Get supported languages
- `account()` - Get account info

## License

MIT License
