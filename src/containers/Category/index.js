import React, { useState } from 'react';
import PropTypes from 'prop-types';
import {
  ScrollView, StyleSheet, View,
} from 'react-native';
import {
  Container, NavBar, ProductList,
} from 'components';
import { scale } from 'react-native-size-matters';
import Colors from 'themes/colors';
import Controls from './Controls';
import Filter from './Filter';

import axios from 'axios';
import baseUrl from '../../../assets/common/baseUrl';
const styles = StyleSheet.create({
  container: {
    backgroundColor: Colors.white,
    borderTopLeftRadius: scale(10),
    borderTopRightRadius: scale(10),
    flex: 1,
  },
  button: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  controls: {
    flexDirection: 'row',
    paddingHorizontal: scale(14),
    justifyContent: 'space-between',
    paddingVertical: scale(14),
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.gray25,
  },
  divider: {
    borderLeftWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.gray25,
  },
  filter: {
    marginLeft: scale(5),
  },
});

const Category = ({ navigation, route }) => {
  const { title,isfor=null,id=null } = route.params;
  const [display, setDisplay] = useState('grid');
  const [row , setRow] = React.useState([]);
  React.useEffect( async()=>{
    console.log(title);
    if (isfor) {
      if (isfor=='search') {
        axios
        .post(`${baseUrl}search`,{search:title})
        .then((res)=>{
          if(res.data.Status.Code == 200){
            console.log('HELLOW WORLD',res.data)
            setRow(res.data.Data);
          }
        })
        .catch((error)=>{
         
          console.log("NOT send" ,error )
        })
      }
      if (isfor=='category') {
       await axios
        .get(`${baseUrl}category/${id}`)
        .then((res)=>{
          if(res.data.Status.Code == 200){
            console.log('HELLOW WORLD',res.data)
            setRow(res.data.Data);
          } 
        })
        .catch((error)=>{
         
          console.log("NOT send" ,error )
        })
      }
      if (isfor=='Ccategory') {
        await axios
         .get(`${baseUrl}category?child=${id}`)
         .then((res)=>{
           if(res.data.Status.Code == 200){
             console.log('HELLOW WORLD',res.data)
             setRow(res.data.Data);
           } 
         })
         .catch((error)=>{
          
           console.log("NOT send" ,error )
         })
       }
       if (isfor=='brands') {
        await axios
         .get(`${baseUrl}brand/${id}`)
         .then((res)=>{
           if(res.data.Status.Code == 200){
             console.log('HELLOW WORLD',res.data)
             setRow(res.data.Data);
           } 
         })
         .catch((error)=>{
          
           console.log("NOT send" ,error )
         })
       }
      
    }  
  },[])
  return (
    <Container backgroundColor="primary" asGradient>
      <NavBar
        title={title}
        onLeftIconPress={() => navigation.goBack()}
        renderRightComponent={() => <Filter />}
      />
      <View style={styles.container}>
        <Controls onDisplayChange={setDisplay} />
        <ScrollView>
          <ProductList isfor={isfor} navigation={navigation} products={row} variant={display} />
        </ScrollView>
      </View>

    </Container>
  );
};

Category.propTypes = {
  navigation: PropTypes.object.isRequired,
  route: PropTypes.object.isRequired,
};

export default Category;
