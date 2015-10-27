<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
class PhoneParser
{
    public $prefixMap = [];

    public function __construct()
    {
        $xml = new SimpleXMLElement(file_get_contents('countries.xml'));

        $data = get_object_vars($xml->Country);

        foreach ($data as $country) {
            $prefix = (string) $country->prefix;
            if (!strlen($prefix)) {
                continue;
            }
            $this->prefixMap[$prefix] = (string) $country->iso;
        }
    }
    /**
     * @param $input
     * @return array
     */
    public function parse($input)
    {
        // removing non digits and separators
        $input = preg_replace('/[^\d, ]/', '', $input);
        // replace all the separators with space
        $input = str_replace([','], ' ', $input);
        // replace multiple spaces with one
        $input = preg_replace('/[[:blank:]]+/', ' ', $input);

        $phones = explode(' ', $input);
        // remove empty phones
        $phones = array_filter($phones);

        $result = [];
        foreach ($phones as $phone) {
            $result[] = $this->parsePhone($phone);
        }

        return $result;
    }

    /**
     * @param $input
     * @return Phone
     */
    protected function parsePhone($input)
    {
        $phone = new Phone();
        $phone->phone = $input;
        // invalid phone length
        if (strlen($input) < 10 || strlen($input) > 14) {
            $phone->checked = false;
            return $phone;
        }

        $this->findCountryCode($phone);
        return $phone;
    }

    /**
     * @param Phone $phone
     * @return Phone
     */
    protected function findCountryCode(Phone $phone)
    {
        $code = substr($phone->phone, 0, strlen($phone->phone)-10);

        // looking for prefix
        foreach ($this->prefixMap as $prefix=>$iso) {
            if (strpos($code, (string) $prefix) === 0) {
                $phone->iso = $iso;
                $phone->countryCode = (string) $prefix;
                $phone->checked = true;
                break;
            }
        }
    }
}