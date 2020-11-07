<?php
/**
 * Created by PhpStorm.
 * User: 一叶
 * Date: 2019/7/24 0024
 * Time: 14:18
 */
namespace app\classes\helper;
use PHPExcel;
use PHPExcel_IOFactory;
class ExcelHelper
{

    /*
     * 导入数据(xlsx,xls,csv)
     * @param string $filename 文件名
     * @param array $filedList 字段
     * @param bool $isCutting 是否切割数组
     * @param int $size 切割数
     * @return array $data
     */
    public static function import($filename, $filedList = [], $isCutting = false, $size = 1000)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        vendor("PHPExcel.PHPExcel");

        switch ($ext) {

            case 'xlsx':
                $objReader   = PHPExcel_IOFactory::createReader('Excel2007');//new \PHPExcel_Reader_Excel2007();
                $objPHPExcel = $objReader->load($filename);//加载文件
                break;
            case 'xls':
                $objReader   = PHPExcel_IOFactory::createReader('Excel5');//new \PHPExcel_Reader_Excel5();
                $objPHPExcel = $objReader->load($filename);//加载文件
                break;
            case 'csv':
                $objReader   = PHPExcel_IOFactory::createReader('CSV'); //new \PHPExcel_Reader_CSV();
                $objPHPExcel = $objReader->setInputEncoding('UTF-8')->load($filename);//加载文件
                break;
            default:
                return [];
                break;
        }

        $highestRow     = $objPHPExcel->getSheet()->getHighestRow();    //取得总行数
        $highestColumn  = $objPHPExcel->getSheet()->getHighestColumn(); //取得总列数

        $cellList = [];

        foreach ($filedList as $k => $v) $cellList[] = self::IntToChr($k);

        //if ($cellList[count($filedList) - 1] != $highestColumn) return [];

        $data = [];

        for ($i = 0; $i < $highestRow - 1; $i++) {

            for ($j = 0; $j < count($cellList); $j++) {

                $data[$i][$filedList[$j]] = $objPHPExcel->getActiveSheet()->getCell($cellList[$j] . ($i + 2))->getFormattedValue();

                if (is_object($data[$i][$filedList[$j]])) $data[$i][$filedList[$j]] = $data[$i][$filedList[$j]]->__toString();

                $data[$i][$filedList[$j]] = preg_replace("/(\s|\ \;|　|\xc2\xa0)/", "", $data[$i][$filedList[$j]]);

            }

        }

        if ($isCutting)
            return array_chunk($data, $size);

        return $data;
    }

    /**
     * 生成Excel列标
     * @param int $index 索引值
     * @param int $start 字母起始值
     * @return string 返回字母
     */
    protected static function IntToChr($index, $start = 65)
    {
        $str = '';
        if (floor($index / 26) > 0) {
            $str .= self::IntToChr(floor($index / 26) - 1);
        }
        return $str . chr($index % 26 + $start);
    }

    /*
     * 导出数据
     * @param array $expTableData 列数据
     * @param array $expCellName  列名
     * @param array $expTitle 导出文件名
     */
    public function export($expTableData,$expCellName,$expTitle)
    {
        $xlsTitle = $expTitle;//iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $xlsTitle;//'结算单'.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);

        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
        }

        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $value = $expTableData[$i][$expCellName[$j][0]];//iconv('utf-8', 'gb2312', $expTableData[$i][$expCellName[$j][0]]);
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $value);
            }
        }

        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

}