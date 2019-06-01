# Introduction

[CodeIgniter](https://codeigniter.com) is without doubt one of the most powerful PHP Framework. it is built with small foot print, simple with ability to create full-featured web applications.

Since we created KoolReport we received many questions like "_How to use KoolReport in CodeIgniter?_". The answer is KoolReport was designed to work with any PHP Frameworks and CodeIgniter is one of them. The setting to make them work together is simple but we want to make things simpler.

So we created this __CodeIgniter pakage__, an extension to let KooLReport work seamlessly inside CodeIgniter environment and providing powerful reporting capability for your CI application. This package will help us to

1. Access CodeIgniter database in report created with Koolreport
2. Automatically publish report resources to CodeIniter's assets folder

All with a simple line of code

```
use \koolreport\codeigniter\Friendship;
```

# Requirement

1. KoolReport >= 2.75.0
2. CodeIgniter >= 3.0.0 

# Installation

## By downloading .zip file

1. [Download](https://www.koolreport.com/packages/codeigniter)
2. Unzip the zip file
3. Copy the folder `codeigniter` into `koolreport` folder so that look like below

```bash
koolreport
├── core
├── codeigniter
```

## By composer

```
composer require koolreport\codeigniter
```

# Documentation

## Friendship

In order for a report to access CodeIgniter databases, we will claim the friendship with CodeIgniter in the report.

```
class MyReport extends \koolreport\KoolReport
{
    use \koolreport\codeigniter\Friendship;// All you need to do is to claim this friendship

    function setup()
    {
        //Now you can access database that you configured in codeigniter
        $this->src("sale_database")
        ->query("select * from orders")
        ->pipe($this->dataStore("orders"));
    }
}
```

As you may see from above code, our `MyReport` now can access all database resources of CodeIgniter through simple line of code. Moreover, the MyReport will configured itself to publish its neccessary resources to public folder of CodeIgniter. Everything just work!


## Adding another datasources

In some cases, you have other sources of data that you would like to include into the report, you can just write normal `settings()` function as you normally do in KoolReport.

```
class MyReport extends \koolreport\KoolReport
{
    use \koolreport\codeigniter\Friendship;

    function settings()
    {
        return array(
            "dataSources"=>array(
                "csv_source"=>array(
                    "class"=>'\koolreport\datasources\CSVDataSource',
                    'filePath'=>dirname(__FILE__)."\mycsvdata.csv",
                )
            )
        );        
    }

    function setup()
    {
        //Now you can access database that you configured in codeigniter
        $this->src("sale_database")
        ->query("select * from orders")
        ->pipe($this->dataStore("orders"));

        $this->src("csv_source")
        ->pipe(...)
        ...
        ->pipe($this->dataStore("csv"));
    }
}
```

As you can see from above code, you have access both to `sale_database` from CodeIgniter as well as extra `csv_source`.

## Customize assets location

By default, the report which has friendship with CodeIgniter will automatically export all of its resources to default location which is `{project_folder}/assets/koolreport_assets`. But in any case, you do not like this settings or just want to organize folder differently you can manually set it up like below:

```
class MyReport extends \koolreport\KoolReport
{
    use \koolreport\codeigniter\Friendship;

    function settings()
    {
        return array(
            "assets"=>array(
                "url"=>"myassets",
                "path"=>"../myassets" // or "path"=>"/var/html/CIProject/myassets"
            )
        );        
    }
```

The `path` can be relative path from your report to assets folder or it can be absolute path.

The `url` is url to assets folder that can be accessed through browser.

# Support

Please use our forum if you need support, by this way other people can benefit as well. If the support request need privacy, you may send email to us at __support@koolreport.com__.