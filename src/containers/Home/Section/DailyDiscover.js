import React from 'react';
import { ProductList } from 'components';
import PropTypes from 'prop-types';
import baseUrl from '../../../../assets/common/baseUrl';
import axios from 'axios';

const DailyDiscover = ({ navigation }) => {
  const [dproduct , setDproduct] = React.useState([])
  axios
  .get(`${baseUrl}products/top/10`)
  .then((res)=>{
    setDproduct(res.data.Data)
    // console.log(res.data.Data)
  })
  .catch((error)=>{
    console.log("something wrong")
  })
  return(
  <ProductList
    navigation={navigation}
    title="Top Products"
    products={dproduct}
  />
  )};

DailyDiscover.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default DailyDiscover;
